<?php

namespace App\Http\Controllers\Publisher;

use App\Models\User;
use App\Models\Setting;
use App\Models\Director;
use App\Models\Location;
use App\Models\Collection;
use App\Helper\CustomTCPDF;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CollectionRequestController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }
    public function index(Request $request)
    {

        $data = [
            'title'   => 'Permintaan Baru',
            'content' => 'publisher.collection.request_list'
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'publisher_id',
            'title',
            'code',
            'collection_problem',
            'problem',
            'validate_at',
            'validated_by'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;

        $totalData = Collection::where('publisher_id', $publisher_id)
            ->where('status', 2)
            ->where('parent_id', 0)
            ->where(function ($query) use ($request) {
                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('validated_at', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('validated_at', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('validated_at', '<', $request->periode_end);
                } else {
                    $query->whereNotNull('validated_at');
                }
            })
            ->count();
        if (empty($search)) {
            $queryData = Collection::where('publisher_id', $publisher_id)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->where(function ($query) use ($request) {

                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('validated_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('validated_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('validated_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('validated_at');
                    }
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where('publisher_id', $publisher_id)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->where(function ($query) use ($request) {

                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('validated_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('validated_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('validated_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('validated_at');
                    }
                })
                ->count();
        } else {
            $queryData = Collection::where('publisher_id', $publisher_id)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->where(function ($query) use ($request) {


                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('validated_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('validated_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('validated_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('validated_at');
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where('publisher_id', $publisher_id)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->where(function ($query) use ($request) {


                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('validated_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('validated_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('validated_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('validated_at');
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $response['data'][] = [
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<a href="' . url('publisher/collection/monitoring/detail/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    date('d-m-Y', strtotime($val->created_at)),
                    '
                        <button type="button" onclick="requestFile(' . $val->id . ')" class="btn btn-success btn-sm"><i class="la la-file-archive-o"></i> Request File Original</button>
                    '
                ];
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function requestOriginal(Request $request, $collectionId)
    {

        $collection = Collection::find($collectionId);

        if ($request->ajax()) {

            $token = sprintf("%06d", mt_rand(1, 999999));
            $file = $request->file('file_request_letter');
            $fileName = Storage::disk($this->location->location)->put('public/collection/request/letter', $file);
            CollectionRequest::create([
                'collection_id'     => $collection->id,
                'request_letter'    => $fileName,
                'token_download'    => $token,
                'count_download'    => 0,
                'status'            => 1,
                'location_id'       => $this->location->id
            ]);

            session()->flash('success', 'Mohon menunggu verifikasi dari admin!');
            $response = ['status'  => 200];
            return response()->json($response);
        } else {

            if ($collection->type == 1) {
                $data = [
                    'title'               => 'Form Request Download File Original Buku',
                    'content'             => 'publisher.book.request_file',
                    'typeId'              => $collection->type,
                    'collectionId'        => $collection->id,
                ];

                return view('publisher.layout.index', ['data' => $data]);
            }
        }
    }

    public function requestReceipt(Request $request, $collectionId)
    {

        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;

        $collection = Collection::where('id', $collectionId)
            ->where('publisher_id', $publisher_id)
            ->where('status', 2)
            ->first();
        if (!$collection) {
            return redirect()->back()->with(['failed' => 'Gagal mendownload resi!']);
        }


        $templateHeader             = Setting::where('slug', 'template-email-header')->first();
        $template                   = Setting::where('slug', 'template-email-collection-success')->first();
        $templateFooter             = Setting::where('slug', 'template-email-footer')->first();
        if ($collection->type == 1 || $collection->type == 2 || $collection->type == 3 || $collection->type == 4) {
            $collMedia = $collection->collectionMedia->where('type', 2)->first();
        } else if ($collection->type == 5) {
            $collMedia = $collection->collectionMedia->where('type', 2)->first();
        } else if ($collection->type == 6) {
            $collMedia = $collection->collectionMedia->where('type', 2)->first();
        } else {
            $collMedia = null;
        }

        //director
        $director = Director::orderByRaw('DATE(position_start) DESC')
            ->first();

        if ($director) {

            $link_signature = public_path('storage/' . str_replace('public/', '', $director->signature));;

            $signature = $director->position . '<br/><img src="' . $link_signature . '" style="max-height:50px; max-width:50px;"><br/><br/>' . $director->name . '<br/>NIP. ' . $director->nip;
        } else {
            $signature = '';
        }



        $number = '';

        if ($collection->received_at != null) {
            $month = (int) date('m', strtotime($collection->received_at));
            $number = $collection->deposit . '/DPB/0.1/' . GeneralHelper::numberToRomawi($month) .  '/' . date('Y', strtotime($collection->received_at));
        }

        $link_header      = Storage::disk('storage1')->path($templateHeader->content); //public_path('storage/' . str_replace('public/', '', $templateHeader->content));
        $link_footer      = Storage::disk('storage1')->path($templateFooter->content); //public_path('storage/' . str_replace('public/', '', $templateFooter->content));


        $data = [
            'received_at'   => date('d F Y', strtotime($collection->received_at)),
            'code'          => $collection->code,
            'publisher'     => $user->publisher->name,
            'title'         => $collection->title,
            'depositid'     => isset($collection->deposit) ? $collection->deposit : '',
            'mimes'         => isset($collMedia->mimes) ? $collMedia->mimes : '',
            'hash'          => isset($collMedia->hash) ? $collMedia->hash : '',
            'size'          => isset($collMedia->size) ? $collMedia->size : '',
            'director'      => $signature,
            'no_resi'       => $number,
            'header'        => '<img src="' . $link_header . '" style="max-width:100%;">',
            'footer'        => '<img src="' . $link_footer . '" style="max-width:100%; margin-bottom:10px">',
        ];

        $html = $template->parse($data);
        // $html .= $template->parse($data);
        // if($templateFooter) {
        //     $html .= $templateFooter->parse($data);
        // }

        $pdf = new CustomTCPDF();
        $pdf->SetMargins(10, 5, 10, 0);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = storage_path("app/public/receipt/$collection->title.pdf");
        return $pdf->output($filename, 'I');
    }


    public function saveTemporary(Request $request)
    {
        $cover    = $request->file('cover_field');
        $original = $request->file('original_field');

        $path_cover    = Storage::disk($this->location->location)->put('public/collection/serial/temporary', $cover);
        $path_original = Storage::disk($this->location->location)->put('public/collection/serial/temporary', $original);

        $cover_image = '<a href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';

        $original_file = '<form method="GET" action="' . url('publisher/collection/stream_file_pdf') . '" target="_blank">
            <input type="hidden" name="csrf-token" value="' . csrf_token() . '">
            <input type="hidden" name="file_stream" value="' . $path_original . '">
            <button type="submit" class="btn btn-success btn-sm">Lihat File</button>
        </form>';

        return response()->json([
            'date_field'     => date('d-m-Y', strtotime($request->date_field)),
            'cover_field'    => $cover_image,
            'original_field' => $original_file,
            'cover_path'     => $path_cover,
            'original_path'  => $path_original,
        ]);
    }

    public function streamFilePdf(Request $request)
    {
        $data = Storage::disk($this->location->location)->path($request->file_stream);

        return response()->make(file_get_contents($data), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="temporary.pdf"'
        ]);
    }

    public function destroy($id)
    {
        $destroy = Collection::where('id', $id)->delete();
        if ($destroy) {
            $collection = Collection::where('parent_id', $id);
            if ($collection->count() > 0) {
                $collection->delete();
            }

            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            $data = Collection::withTrashed()->find($id);
            activity('collections')
                ->performedOn(new Collection())
                ->causedBy(session('id'))
                ->withProperties(['judul' => $data->title])
                ->log('Menghapus data koleksi');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
