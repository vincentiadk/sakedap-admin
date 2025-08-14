<?php

namespace App\Http\Controllers\Admin;

use \TCPDF;
use App\Models\Problem;
use App\Models\Setting;
use App\Models\Director;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Expedition;
use App\Models\Contributor;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use App\Models\CopyRejected;
use App\Models\DeliveryForm;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionCopy;
use App\Models\LibraryLocation;
use App\Models\CopyRejectedProblem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CollectionDeliveryController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index($type = null)
    {
        $get_deposit_head = DepositHead::get();
        $library_id = session('library_id');
        $deposit_head = [];
        foreach ($get_deposit_head as $key => $value) {
            $deposit_head[$value['category']] = $value['shape'];
        }

        $data =  [
            'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
            'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
            'deposit_head' => $deposit_head,
            'title'   => 'Pengiriman Koleksi',
            'expedition' => Expedition::get(),
            'content' => 'admin.delivery.manage'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'action',
            'publisher',
            'count_title',
            'count_exemplar',
            'status',
            'expedition',
            'receipt_no',
            'delivery_date',
            'library_id'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');

        $totalData = DeliveryForm::count();

        $queryData = DeliveryForm::whereIn('status', ['DELIVERED', 'ACCEPTED'])
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
                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
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
                if (session('library_id') <> 1) {
                    $query->where('library_id', session('library_id'));
                }
            })->offset($start)->limit($length)->orderBy($order, $dir)->get();

        $totalFiltered = DeliveryForm::whereIn('status', ['DELIVERED', 'ACCEPTED'])
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
                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
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
                if (session('library_id') <> 1) {
                    $query->where('library_id', session('library_id'));
                }
            })->count();

        $nomor = 1;

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $action = '';

                if ($val->status == 'DELIVERED') {
                    if (session('library_id') == 1 &&  $val->library->id <> 1) {
                        $action = 'Menunggu Diterima Provinsi';
                    } else {
                        $action = '<a href="' . url('admin/collection/delivery/review/' . $val->id) . '" class="btn btn-info btn-sm"><i class="la la-eye"></i> Detail</a>';
                    }
                } elseif ($val->status == 'ACCEPTED') {
                    $action = '<a href="' . url('admin/collection/delivery/download_receipt/' . $val->letter_no) . '" class="btn btn-primary btn-sm"><i class="la la-file"></i> Download</a>';
                }

                $response['data'][] = [
                    $nomor,
                    $action,
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    $val->collectionCopy->groupBy('collection_id')->count(),
                    $val->collectionCopy->count(),
                    $val->status,
                    $val->expedition->name,
                    $val->receipt_no,
                    date('d-m-Y', strtotime($val->delivery_date)),
                    $val->library->name
                ];

                $nomor++;
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
        $delivery->collectionCopy->each(function ($copy) {
            $copy->update(['availability' => '8']);
        });

        $data =  [
            'delivery' => $delivery,
            'problem'       => Problem::all(),
            'title'   => 'Detail Pengiriman KC dan KR Analog',
            'content' => 'admin.delivery.review',
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function accept(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'accepted_date' => 'required',
        ], [
            'accepted_date.required' => 'Tanggal terima wajib di isi!',
        ]);
        // dd($validation);
        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];

            return response()->json($response);
        }

        $delivery = DeliveryForm::find($id);
        $delivery->status = 'ACCEPTED';
        $delivery->accepted_date = $request->accepted_date;
        $delivery->letter_no = GeneralHelper::letterNoDelivery();
        $delivery->save();

        foreach ($request->collection_copy as $key => $value) {

            $copy = CollectionCopy::find($value['id']);
            $lib_loc = LibraryLocation::where('library_id', session('library_id'))->where('publish', '1')->first();
            if (!empty($lib_loc)) {
                $copy->lib_loc_id = $lib_loc->id;
            }

            if ($value['status'] == 'accept') {
                $copy->availability = '10';
                $copy->condition = $value['status_accept'];
                $copy->received_at = date('Y-m-d H:i:s');
                $copy->received_by = session('id');

                $collection = $copy->collection;
                $publisher = Publisher::find($collection->publisher_id);
                $province_id = isset($publisher->province_id) ? $publisher->province_id : null;
                $city_id = isset($publisher->city_id) ? $publisher->city_id : null;
                if (session('library_id') == 1) {
                    $copy->collection->mark_national = GeneralHelper::generateMarks($collection->deposit_head_id, $province_id);
                } else {
                    $copy->collection->mark_province = GeneralHelper::generateMarks($collection->deposit_head_id, $province_id, $city_id);
                }
                $collection->save();
            } else {
                $copy->availability = '11';
                $copy->condition = null;

                $copyRejected = CopyRejected::create([
                    "collection_copy_id" => $copy->id
                ]);

                foreach ($value['status_reject'] as $k => $v) {
                    CopyRejectedProblem::create([
                        'copy_rejected_id' => $copyRejected->id,
                        'problem_id' => $v
                    ]);
                }
            }
            $copy->save();
        }


        session()->flash('success', 'Berhasil diverifikasi!');
        $response = ['status' => 200, 'id' => $delivery->id];
        return response()->json($response);
    }

    public function downloadReceipt($letter_no)
    {
        $delivery = DeliveryForm::where('letter_no', $letter_no)->first();

        // dd($delivery);
        if ($delivery) {
            $template           = Setting::where('slug', 'template-email-delivery-receipt')->first();
            $header             = Setting::where('slug', 'template-email-header')->first();
            $footer             = Setting::where('slug', 'template-email-footer')->first();
            $link_header        = public_path('storage/' . str_replace('public/', '', $header->content));
            $link_footer        = public_path('storage/' . str_replace('public/', '', $footer->content));
            $received_at        = date('Y-m-m', strtotime($delivery->accepted_date));
            $director_signature = Director::where('province_id', session('province_id'))->whereDate('position_start', '<', $received_at)->first();
            $director = $director_signature;

            if ($director->signature) {
                $signature_image = public_path('storage/' . str_replace('public/', '', $director->signature));
            } else {
                $signature_image = '';
            }

            $url = url('admin/collection/delivery/download_receipt/' . $delivery->letter_no);

            $signature = '
                ' . $director->position . '<br><br>
                <img src="' . $signature_image . '" style="max-width:40px !important;"><br><br>
                ' . $director->name . '<br>
                <span style="font-weight:bold;">' . $director->nip . '</span>
            ';

            $data = [
                'accepted_date' => date('d F Y', strtotime($delivery->accepted_date)),
                'letter_no'        => $delivery->letter_no,
                'publisher_name'   => $delivery->publisher->name,
                'director'    => $signature,
                'header'      => '<img src="' . $link_header . '" style="max-width:100%;">',
                'footer'      => '<img src="' . $link_footer . '" style="max-width:100$; margin-bottom:10px">',
                'qr'    => 'https://image-charts.com/chart?chs=150x150&cht=qr&chl=' . $url,
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

                // $count = CollectionCopy::where('id', $value->id)->where('collection_id',$value->collection_id)->count();
                // dd($count);

                $html_collections .= '<tr>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . ($key + 1) . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . date('d-m-Y', strtotime($delivery->accepted_date)) . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->title . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->depositHead->shape . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $collection->code . '</td>';
                $html_collections .= '<td style="padding:10px;text-align: center;">' . $value->count . '</td>';

                $trk_no = '';
                if ($delivery->library_id == 1) {
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
}
