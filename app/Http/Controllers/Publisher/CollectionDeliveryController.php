<?php

namespace App\Http\Controllers\Publisher;

use \TCPDF;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Problem;
use App\Models\Category;
use App\Models\Contributor;
use App\Models\Collection;
use App\Models\Expedition;
use App\Models\Publisher;
use App\Models\DeliveryForm;
use App\Models\Library;
use App\Models\DepositHead;
use App\Models\Setting;
use App\Models\Director;
use App\Models\Location;
use App\Models\CollectionCopy;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use falahati\PHPMP3\MpegAudio;
use App\Models\CollectionProblem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use LynX39\LaraPdfMerger\Facades\PdfMerger;

class CollectionDeliveryController extends Controller
{

    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index($connect = null)
    {

        $publisher = User::find(session('id'))->publisher;

        $data = [
            'title'   => 'Pengiriman KC dan KR Analog',
            'content' => 'publisher.collection.monitor_delivery',
            'expedition' => Expedition::get(),
            'groups' => $publisher->getGroups()
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $user = User::find(session('id'));
        $publisher = $user->publisher;

        $publisher_params  = $request->input('publisher_id');

        if ($publisher_params == null) {
            if ($publisher->getGroups() == null) {
                $publisher_id[0] =  $publisher->id;
            } else {
                $publisher_id = $publisher->getGroups()->groups->pluck('publisher_id');
            }
        } else {
            $publisher_id =  $publisher_params;
        }

        $model = DeliveryForm::whereIn('publisher_id', $publisher_id)
            ->where(function ($query) use ($request) {
                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('delivery_date', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('delivery_date', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('delivery_date', '<', $request->periode_end);
                } else {
                    $query->whereNotNull('delivery_date');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->receipt_no) {
                    $query->where('receipt_no', $request->receipt_no);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->expedition_id) {
                    $query->where('expedition_id', $request->expedition_id);
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->where('status', $request->status);
                }
            });

        $totalData = $model->count();
        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $totalFiltered = $model->where(function ($query) use ($search) {
                $query->where('receipt_no', 'like', "%{$search}%");
            })
                ->count();
            $queryData = $model->where(function ($query) use ($search) {
                $query->where('receipt_no', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {

                $action = '';
                if ($val->status == 'DRAFT') {
                    $action = '
                        <a href="' . url('publisher/collection/delivery/detail/' . $val->id) . '" class="btn btn-info btn-sm"><i class="la la-eye"></i> Edit</a>
                        <a href="' . url("publisher/collection/delivery/download_shipping/$val->id") . '" class="btn btn-primary btn-sm"><i class="la la-file"></i> Bukti Kirim</a>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>         
                    ';
                } elseif ($val->status == 'DELIVERED') {
                    $action = '
                        <a href="' . url("publisher/collection/delivery/download_shipping/$val->id") . '" class="btn btn-primary btn-sm"><i class="la la-file"></i> Bukti Kirim</a>
                    ';
                } elseif ($val->status == 'ACCEPTED') {
                    $action = '
                        <a href="' . url("publisher/collection/delivery/download_shipping/$val->id") . '" class="btn btn-primary btn-sm"><i class="la la-file"></i> Bukti Kirim</a>
                        <a href="' . url("publisher/collection/delivery/download_receipt/$val->letter_no") . '" class="btn btn-primary btn-sm"><i class="la la-file"></i> Bukti Terima</a>
                    ';
                }

                $response['data'][] = [
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    $val->collectionCopy->groupBy('collection_id')->count(),
                    $val->collectionCopy->count(),
                    $val->status,
                    $val->expedition->name,
                    $val->receipt_no,
                    date('d-m-Y', strtotime($val->delivery_date)),
                    $val->library->name,
                    $action
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

    public function review(Request $request, $id)
    {
        $delivery = DeliveryForm::find($id);

        $user = User::find(session('id'));
        $publisher = $user->publisher;

        if ($publisher->getGroups() == null) {
            if ($delivery->publisher_id != $publisher->id) {
                return abort(403, 'Unauthorized action.');
            }
        } else {
            if (!$publisher->checkSameGroups($delivery->publisher_id)) {
                return abort(403, 'Unauthorized action.');
            }
        }

        $getDepositHead = DepositHead::whereIn('category', ['KC', 'KRA'])->get();
        $deposit_head = [];
        $deposit_head_serial = [];
        foreach ($getDepositHead as $key => $value) {
            $deposit_head[$value->id] = $value->shape;
            if ($value->is_serial == 1) {
                $deposit_head_serial[] = $value->id;
            }
        }



        $collections = [];
        foreach ($delivery->collectionCopyDistinct as $key => $value) {
            $collection = $value->collection;
            $publisher = Publisher::with('province', 'city')->where('id', $collection['publisher_id'])->first();

            $log_contributor = [];
            $kepeng = [];
            if ($collection->collectionContributor->count() > 0) {
                foreach ($collection->collectionContributor as $cc) {
                    $kepeng[] = $cc->author->fullname;
                    $log_contributor[] = [
                        'id_kontributor' => $cc->contributor->id,
                        'kontributor' => $cc->contributor->name,
                        'id_author' => $cc->author->id,
                        'author' => $cc->author->fullname,
                        'author_title' => $cc->author->title,
                        'author_birth' => $cc->author->year_of_birth,
                        'author_death' => $cc->author->year_of_death,
                    ];
                }
            }

            $log_category = [];
            if ($collection->collectionCategory->count() > 0) {
                foreach ($collection->collectionCategory as $cc) {
                    $log_category[] = $cc->category->id;
                }
            }

            $exemplar = $delivery->collectionCopy->where('collection_id', $collection->id)->count();

            $collectionMedia = CollectionMedia::where('collection_id', $collection['id'])->where('type', 1)->first();

            $collections[] = [
                'collection_id' => $collection['id'],
                'deposit_head_id' => $collection['deposit_head_id'],
                'code' => $collection['code'],
                'title' => $collection['title'],
                'tahun_terbit' => $collection['publication_year'],
                'bulan_terbit' => $collection['publication_month'],
                'kepeng' => !empty($kepeng) ? implode('; ', $kepeng) : '',
                'sinopsis' => $collection['description'],
                'edisi' => $collection['edition'],
                'jml_hlm' => $collection->physicalDescription() ? $collection->physicalDescription()->total_page : '',
                'subjek' => '',
                'seri' => $collection['serial'],
                'dimension' => $collection->physicalDescription() ? $collection->physicalDescription()->dimension : '',
                'publisher_id' => $publisher ? $publisher->id : '',
                'publisher_name' => $publisher ? $publisher->name : '',
                'publisher_province_id' => $publisher ? $publisher->province_id : '',
                'publisher_province' => $publisher ? $publisher->province->name : '',
                'publisher_city_id' => $publisher ? $publisher->city_id : '',
                'publisher_city' => $publisher ? $publisher->city->name : '',
                'contributor' => $log_contributor,
                'price' => $collection['price'],
                'category' => $log_category,
                'exemplar' => $exemplar,
                'cover_url' => $collectionMedia ? url('/collection/cover') . '/' . $collectionMedia->id  : '',
            ];
        }

        $data =  [
            'collections' => $collections,
            'delivery' => $delivery,
            'publisher'   => $publisher,
            'deposit_head' => $deposit_head,
            'deposit_head_serial' => $deposit_head_serial,
            'category' => Category::where('type', 1)->get(),
            'expedition' => Expedition::get(),
            'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
            'library' => Library::where('province_id', $user->publisher->province_id)->first(),
            'title'   => 'Detail Pengiriman KC dan KR Analog',
            'content' => 'publisher.delivery.review',
        ];
        // dd($data);

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function history(Request $request)
    {

        $history = ActivityLog::where('subject_type', 'App\Models\Collection')
            ->where('subject_id', $request->collection_id)
            ->get();

        return response()->json($history);
    }

    public function downloadReceipt($letter_no)
    {
        $delivery = DeliveryForm::where('letter_no', $letter_no)
            ->first();

        if ($delivery) {
            $template           = Setting::where('slug', 'template-email-delivery-receipt')->first();
            $header             = Setting::where('slug', 'template-email-header')->first();
            $footer             = Setting::where('slug', 'template-email-footer')->first();
            $link_header        = public_path('storage/' . str_replace('public/', '', $header->content));
            $link_footer        = public_path('storage/' . str_replace('public/', '', $footer->content));
            $take_now           = Director::orderByRaw('DATE(position_start) DESC')->first();
            $received_at        = date('Y-m-m', strtotime($delivery->accepted_date));
            $director_signature = Director::whereDate('position_start', '<', $received_at)->first();

            if ($director_signature) {
                $director = $director_signature;
            } else {
                $director = $take_now;
            }

            if ($director->signature) {
                $signature_image = public_path('storage/' . str_replace('public/', '', $director->signature));
            } else {
                $signature_image = '';
            }

            $url = url('admin/collection/delivery/download_receipt/' . $delivery->letter_no);
            $signature = $director->position . '<br><br><img src="' . $signature_image . '" width="150"><br><br>' . $director->name . '<br><span style="font-weight:bold;">NIP. ' . $director->nip . '</span>';

            $data = [
                'accepted_date' => date('d F Y', strtotime($delivery->accepted_date)),
                'letter_no'        => $delivery->letter_no,
                'publisher_name'   => $delivery->publisher->name,
                'director'    => $signature,
                'header'      => '<img src="' . $link_header . '" style="max-width:100%;">',
                'footer'      => '<img src="' . $link_footer . '" style="max-width:100%; margin-bottom:10px">',
                'qr'    => 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . $url,
            ];

            $html = $template->parse($data);
            $pdf  = new TCPDF();
            $pdf->SetMargins(10, 5, 10, 0);
            $pdf->SetAutoPageBreak(true, 0);
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');

            $html_collections = '<table border="1" style="font-size:8px">';
            $html_collections .= '<tr>';
            $html_collections .= '<th style="padding:12px;text-align: center;">No</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Tanggal Terima</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Judul</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Jenis Koleksi</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">ISBN/ISSN</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Jumlah (Eksemplar)</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">TRK</th>';
            $html_collections .= '</tr>';

            $data = CollectionCopy::selectRaw('*, COUNT(*) as count')
                ->where('delivery_form_id', $delivery->id)
                ->where('availability', '<>', '11')
                ->groupBy('collection_id')
                ->get();

            foreach ($data as $key => $value) {
                $collection = $value->collection;

                $count = CollectionCopy::where('id', $value->id)->where('collection_id', $value->collection_id)->count();
                // dd($count);

                $html_collections .= '<tr>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . ($key + 1) . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . date('d-m-Y', strtotime($delivery->accepted_date)) . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->title . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->depositHead->shape . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->code . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $value->count . '</td>';

                $trk_no = '';
                if ($delivery->library_id = 1) {
                    $trk_no = $collection->mark_national;
                } else {
                    $trk_no = $collection->mark_province;
                }
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $trk_no . '</td>';
                $html_collections .= '</tr>';
            }
            $html_collections .= '</table>';

            $pdf->AddPage();
            $pdf->writeHTML($html_collections, true, false, true, false, '');

            $filename = storage_path("app/public/receipt/$delivery->letter_no.pdf");
            return $pdf->output($filename, 'I');
        }

        return redirect()->back();
    }

    public function downloadShipping($id)
    {
        $delivery = DeliveryForm::where('id', $id)
            ->first();
        \Log::info($delivery);
        if ($delivery) {
            // $template           = Setting::where('slug', 'template-email-delivery-receipt')->first();
            // $header             = Setting::where('slug', 'template-email-header')->first();
            // $footer             = Setting::where('slug', 'template-email-footer')->first();
            // $link_header        = public_path('storage/' . str_replace('public/', '', $header->content));
            // $link_footer        = public_path('storage/' . str_replace('public/', '', $footer->content));
            // $take_now           = Director::orderByRaw('DATE(position_start) DESC')->first();
            // $received_at        = date('Y-m-m', strtotime($delivery->accepted_date));
            // $director_signature = Director::whereDate('position_start', '<', $received_at)->first();

            // if($director_signature) {
            //     $director = $director_signature;
            // } else {
            //     $director = $take_now;
            // }

            // if($director->signature) {
            //     $signature_image = public_path('storage/' . str_replace('public/', '', $director->signature));
            // } else {
            //     $signature_image = '';
            // }

            // $url = url('admin/collection/delivery/download_receipt/' . $delivery->letter_no);
            // $signature = $director->position . '<br><br><img src="' . $signature_image . '" width="150"><br><br>' . $director->name . '<br><span style="font-weight:bold;">NIP. ' . $director->nip . '</span>';

            // $data = [
            //     'accepted_date' => date('d F Y', strtotime($delivery->accepted_date)),
            //     'letter_no'        => $delivery->letter_no,
            //     'publisher_name'   => $delivery->publisher->name,
            //     'director'    => $signature,
            //     'header'      => '<img src="' . $link_header . '" style="max-width:100%;">',
            //     'footer'      => '<img src="' . $link_footer . '" style="max-width:100%; margin-bottom:10px">',
            //     'qr'    => 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' . $url,
            // ];

            // $html = $template->parse($data);
            $pdf  = new TCPDF();
            $pdf->SetMargins(10, 5, 10, 0);
            $pdf->SetAutoPageBreak(true, 0);
            $pdf->AddPage();

            $html = '<p style="text-align:center">DAFTAR KIRIM KARYA CETAK / KARYA REKAM</p>

            <p>Tanggal Pengiriman : ' . date('d F Y', strtotime($delivery->delivery_date)) . ' </p>
            
            <p>Ekpedisi : ' . $delivery->expedition->name . ' </p>
            
            <p>Nomor Resi : ' . $delivery->receipt_no . '</p>
            
            <p>Tujuan Pengiriman : ' . $delivery->library->name . '</p>
            
            <br><br>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $html_collections = '<table border="1" style="font-size:8px">';
            $html_collections .= '<tr>';
            $html_collections .= '<th style="padding:12px;text-align: center;">No</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Tanggal Terima</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Judul</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Jenis Koleksi</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">ISBN/ISSN</th>';
            $html_collections .= '<th style="padding:12px;text-align: center;">Jumlah (Eksemplar)</th>';
            if ($delivery->status == 'ACCEPTED') {
                $html_collections .= '<th style="padding:12px;text-align: center;">TRK</th>';
            }
            $html_collections .= '</tr>';

            $data = CollectionCopy::selectRaw('*, COUNT(*) as count')
                ->where('delivery_form_id', $delivery->id)
                /*->where(function($query) {
                        $query->where('availability', '<>', '11')
                        ->orWhere('availability', '');
                    })*/
                ->groupBy('collection_id')
                ->get();
            \Log::info($data);

            foreach ($data as $key => $value) {
                $collection = $value->collection;

                $count = CollectionCopy::where('id', $value->id)->where('collection_id', $value->collection_id)->count();
                // dd($count);

                $html_collections .= '<tr>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . ($key + 1) . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . date('d-m-Y', strtotime($delivery->accepted_date)) . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->title . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->depositHead->shape . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->code . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $value->count . '</td>';

                $trk_no = '';
                if ($delivery->status == 'ACCEPTED') {
                    if ($delivery->library_id = 1) {
                        $trk_no = $collection->mark_national;
                    } else {
                        $trk_no = $collection->mark_province;
                    }
                    $html_collections .= '<td style="padding:10px;text-align: center;">' . $trk_no . '</td>';
                }
                $html_collections .= '</tr>';
            }
            $html_collections .= '</table>';

            $pdf->writeHTML($html_collections, true, false, true, false, '');

            $filename = storage_path("app/public/shipping/$delivery->letter_no.pdf");
            return $pdf->output($filename, 'I');
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $destroy = DeliveryForm::where('id', $id)->delete();
        // dd($destroy);
        if ($destroy) {
            // $collection = Collection::where('parent_id', $id);
            // if ($collection->count() > 0) {
            //     $collection->delete();
            // }

            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            $data = DeliveryForm::withTrashed()->find($id);
            activity('delivery_forms')
                ->performedOn(new DeliveryForm())
                ->causedBy(session('id'))
                ->withProperties(['id' => $data->id])
                ->log('Menghapus data pengiriman');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
