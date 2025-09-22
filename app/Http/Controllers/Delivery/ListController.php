<?php

namespace App\Http\Controllers\Delivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman"),
                'content' => 'delivery.list',
                'plugins' => [
                    'datatable',
                    'select2',
                    'daterangepicker',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'letter.letter_id',
            null,
            'penerbit.name',
            'letter.receipt_no',
            'jasa_pengiriman.name',
            'letter.jumlah_paket',
            'letter.status',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];

        if (Main::isNotCenterBranch()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "letter.jasa_pengiriman_id = $request->delivery_service_id";
        }

        if ($request->status) {
            $whereCondition[] = "letter.status = '$request->status'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "letter.penerbit_id = $request->executor_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(letter.letter_date >= date '$startDate' and letter.letter_date <= date '$endDate')";
        }

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "$c like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        if ($order) {
            $orderColumnIndex = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderBy = "order by " . $column[$orderColumnIndex] . " $orderDir";
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                letter
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                letter
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                letter.*,
                                jasa_pengiriman.name as name_jasa_pengiriman,
                                penerbit.name as name_penerbit
                            from
                                letter
                            left join
                                penerbit on penerbit.id = letter.penerbit_id
                            left join
                                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $statusAccept = ['DITERIMA PENUH', 'DITERIMA PARSIAL'];

                if (in_array($val->STATUS, $statusAccept)) {
                    $action = '
                        <a href="' . url('delivery/list/print/' . $val->LETTER_ID) . '" class="btn btn-success btn-sm" target="_blank">
                            <i class="ph-printer me-1"></i>
                            Resi Penerimaan
                        </a>
                    ';
                } else {
                    $action = '
                        <a href="' . url('delivery/list/verification/' . $val->LETTER_ID) . '" class="btn btn-primary btn-sm">
                            <i class="ph-check me-1"></i>
                            Verifikasi
                        </a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $val->NAME_PENERBIT,
                    $val->RECEIPT_NO,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->JUMLAH_PAKET,
                    $val->STATUS,
                ];

                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function verification(Request $request, $id)
    {
        $letter = QueryAPI::get("
            select
                letter.*,
                jasa_pengiriman.name as name_jasa_pengiriman,
                penerbit.name as name_penerbit
            from
                letter
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
            where
                letter.letter_id = $id
        ", true);

        $letterDetail = QueryAPI::get("
            select
                letter_detail.*,
                worksheets.name as name_worksheet
            from
                letter_detail
            left join
                catalogs on catalogs.id = letter_detail.catalog_id
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            where
                letter_detail.letter_id = $id
        ");

        if ($request->ajax()) {
            try {
                $letterDetailIds = $request->collect('letter_detail_id');
                $quantities = $request->collect('letter_detail_quantity');
                $qtyAccepts = $request->collect('letter_detail_qty_accept');
                $qtyRejects = $request->collect('letter_detail_qty_reject');
                $remarks = $request->collect('letter_detail_remark');
                $param = $request->param;
                $status = 'DITERIMA PENUH';

                $letterDetailsToUpdate = [];
                $collectionsToCreate = [];

                if ($param === 'save-verification' && $letterDetailIds->isNotEmpty()) {
                    $idsInClause = $letterDetailIds->implode(',');

                    $dataCopies = QueryAPI::get("
                        select
                            ld.*,
                            l.letter_date as letter_date_letter,
                            l.branch_id as branch_id_letter,
                            l.create_by as create_by_letter,
                            l.update_by as update_by_letter,
                            l.publisher_id as publisher_id_letter,
                            c.publishlocation as publishlocation_catalog,
                            c.category_id as category_id_catalog,
                            c.worksheet_id as worksheet_id_catalog,
                            c.edition as edition_catalog,
                            c.isopac as isopac_catalog,
                            c.publikasi as publikasi_catalog,
                            c.city_id as city_id_catalog,
                            c.album as album_catalog,
                            c.series as series_catalog,
                            c.volume as volume_catalog,
                            c.publish_month as publish_month_catalog,
                            c.copyright as copyright_catalog,
                            c.preview as preview_catalog,
                            c.akses as akses_catalog
                        from
                            letter_detail ld
                        left join
                            catalogs c on c.id = ld.catalog_id
                        left join
                            letter l on l.letter_id = ld.letter_id
                        where
                            ld.letter_detail_id in ($idsInClause)
                    ");

                    $dataCopies = collect($dataCopies);
                }

                foreach ($letterDetailIds as $key => $ldi) {
                    $qtyAccept = $qtyAccepts->get($key, 0);
                    $qtyReject = $qtyRejects->get($key, 0);
                    $remark = $remarks->get($key, []);
                    $quantity = $quantities->get($key, 0);

                    $letterDetailsToUpdate[] = [
                        'id' => $ldi,
                        'qty_accept' => $qtyAccept,
                        'qty_reject' => $qtyReject,
                        'remark' => is_array($remark) ? implode(';', $remark) : $remark
                    ];

                    if ($qtyAccept < $quantity) {
                        $status = 'DITERIMA PARSIAL';
                    }

                    if ($param === 'save-verification' && $qtyAccept > 0) {
                        $dataCopy = $dataCopies->firstWhere('LETTER_DETAIL_ID', $ldi);

                        if ($dataCopy) {
                            for ($i = 0; $i < $qtyAccept; $i++) {
                                $collectionsToCreate[] = [
                                    'title' => $dataCopy->TITLE,
                                    'author' => $dataCopy->AUTHOR,
                                    'publishlocation' => $dataCopy->PUBLISHLOCATION_CATALOG,
                                    'publisher' => $dataCopy->PUBLISHER,
                                    'publishyear' => $dataCopy->PUBLISH_YEAR,
                                    'edition' => $dataCopy->EDITION_CATALOG,
                                    'physicaldescription' => $dataCopy->DESKRIPSIFISIK,
                                    'isbn' => $dataCopy->ISBN,
                                    'price' => $dataCopy->PRICE,
                                    'tanggalkirim' => $dataCopy->LETTER_DATE_LETTER,
                                    'isdelete' => 0,
                                    'branch_id' => $dataCopy->BRANCH_ID_LETTER,
                                    'catalog_id' => $dataCopy->CATALOG_ID,
                                    'category_id' => $dataCopy->CATEGORY_ID_CATALOG,
                                    'media_id' => $dataCopy->COLLECTIONMEDIAID,
                                    'status' => 'Tersedia',
                                    'createby' => $dataCopy->CREATE_BY_LETTER,
                                    'createdate' => date('Y-m-d H:i:s'),
                                    'createterminal' => request()->ip(),
                                    'updateby' => $dataCopy->UPDATE_BY_LETTER,
                                    'updatedate' => date('Y-m-d H:i:s'),
                                    'updateterminal' => request()->ip(),
                                    'kalaterbit' => $dataCopy->KALA_TERBIT,
                                    'worksheet_id' => $dataCopy->WORKSHEET_ID_CATALOG,
                                    'isverified' => 1,
                                    'cleaning_note' => $dataCopy->CLEANING_NOTE,
                                    'publisher_id' => $dataCopy->PUBLISHER_ID_LETTER,
                                    'edisiserial' => $dataCopy->EDISI_SERIAL,
                                    'isopac' => $dataCopy->ISOPAC_CATALOG,
                                    'publikasi' => $dataCopy->PUBLIKASI_CATALOG,
                                    'ttes_awal' => $dataCopy->TTES_AWAL,
                                    'ttes_akhir' => $dataCopy->TTES_AKHIR,
                                    'penerbit_id' => $dataCopy->PUBLISHER_ID_LETTER,
                                    'is_receive_date_marked' => $dataCopy->IS_RECEIVEDATE,
                                    'city_id' => $dataCopy->CITY_ID_CATALOG,
                                    'album' => $dataCopy->ALBUM_CATALOG,
                                    'series' => $dataCopy->SERIES_CATALOG,
                                    'volume' => $dataCopy->VOLUME_CATALOG,
                                    'publish_month' => $dataCopy->PUBLISH_MONTH_CATALOG,
                                    'copyright' => $dataCopy->COPYRIGHT_CATALOG,
                                    'preview' => $dataCopy->PREVIEW_CATALOG,
                                    'problem' => $dataCopy->REMARK,
                                    'letter_id' => $dataCopy->LETTER_ID,
                                    'letter_detail_id' => $ldi,
                                    'akses' => $dataCopy->AKSES_CATALOG,
                                ];
                            }
                        }
                    }
                }

                foreach ($letterDetailsToUpdate as $updateData) {
                    $letterId = $updateData['id'];

                    unset($updateData['id']);

                    QueryAPI::update('letter_detail', $letterId, $updateData, false);
                }

                if (!empty($collectionsToCreate)) {
                    foreach ($collectionsToCreate as $collectionData) {
                        $createCollection = QueryAPI::create('collections', $collectionData, false);

                        if ($createCollection) {
                            QueryAPI::update('collections', $createCollection->ID, [
                                'nomorbarcode' => sprintf('%011d', $createCollection->ID)
                            ], false);
                        }
                    }
                }

                QueryAPI::update('letter', $id, [
                    'status' => ($param === 'save-verification') ? $status : $request->status,
                    'accept_date' => ($param === 'save-verification') ? date('Y-m-d H:i:s') : null
                ], false);

                $response = [
                    'code' => 200,
                    'message' => 'Data telah disimpan'
                ];
            } catch (\Exception $e) {
                $response = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        }

        return view('layouts.index', [
            'data' => [
                'letter' => $letter,
                'letterDetail' => $letterDetail,
                'disabled' => in_array(($letter->STATUS ?? ''), ['DALAM PENGIRIMAN', 'TERKIRIM', 'CEK FISIK']) ? null : 'disabled',
                'content' => 'delivery.list-verification',
                'plugins' => [
                    'select2',
                    'datatable',
                ]
            ]
        ]);
    }

    public function print($id)
    {
        if (!isset($id)) {
            return redirect('delivery/list');
        }

        $letter = QueryAPI::get("
            select
                letter.*,
                penerbit.name as name_penerbit
            from
                letter
            left join
                penerbit on penerbit.id = letter.penerbit_id
            where
                letter.letter_id = $id
        ", true);

        if (!$letter) {
            return redirect('delivery/list');
        }

        $settings = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug in ('ResiPenerimaan','Header','Footer')
        ");

        $templateEmailContent = null;
        $templateEmailHeader = null;
        $templateEmailFooter = null;

        if ($settings) {
            foreach ($settings as $setting) {
                if ($setting->SLUG == 'ResiPenerimaan') {
                    $templateEmailContent = $setting;
                } elseif ($setting->SLUG == 'Header') {
                    $templateEmailHeader = $setting;
                } elseif ($setting->SLUG == 'Footer') {
                    $templateEmailFooter = $setting;
                }
            }
        }

        if (!$templateEmailContent || !$templateEmailHeader || !$templateEmailFooter) {
            return redirect('delivery/list');
        }

        $branchId = session('branch_id');
        $dateNow = date('Y-m-d');
        $signature = '';

        $leader = QueryAPI::get("
            select
                *
            from
                penanggung_jawab
            where
                branch_id = $branchId and
                (
                    tanggal_awal <= date '$dateNow' and
                    tanggal_akhir >= date '$dateNow'
                )
        ", true);

        if ($leader) {
            $signatureUrl = url('stream-file') . '?type=gambar_ttd&id=' . ($leader->ID ?? '') . '&filename=' . ($leader->TTD_FILE_NAME ?? '');
            $signature = $leader->JABATAN . '<br><br>' . '<img src="' . $signatureUrl . '" style="max-width:40px !important;"><br><br>' . $leader->NAMA . '<br>' . '<span style="font-weight:bold;">' . $leader->NIP . '</span>';
        }

        $dataParseTemplate = [
            'accepted_date' => date('d/m/Y', strtotime($letter->ACCEPT_DATE)),
            'letter_no' => $letter->LETTER_NUMBER_UT,
            'publisher_name' => $letter->NAME_PENERBIT,
            'director' => $signature,
            'header' => '<img src="' . url('stream-file?type=gambar_template&id=' . ($templateEmailHeader->ID ?? '') . '&filename=' . ($templateEmailHeader->CONTENT ?? '')) . '" style="max-width:100%;">',
            'Footer' => '<img src="' . url('stream-file?type=gambar_template&id=' . ($templateEmailFooter->ID ?? '') . '&filename=' . ($templateEmailFooter->CONTENT ?? '')) . '" style="max-width:100%; margin-bottom:10px">',
            'qr' => 'https://image-charts.com/chart?chs=150x150&cht=qr&chl=' . url()->current(),
        ];

        $pdf = new \TCPDF();
        $pdf->SetMargins(10, 5, 10, 0);
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->AddPage();
        $html = Main::parseTemplateEmail($dataParseTemplate, $templateEmailContent);
        $pdf->writeHTML($html, true, false, true, false, '');

        $collections = QueryAPI::get("
            select
                collections.letter_id,
                collections.title,
                collections.isbn,
                collections.mark_province,
                collections.mark_national,
                collections.branch_id,
                letter_detail.qty_accept as qty_accept_letter_detail,
                letter.accept_date as accept_date_letter,
                worksheets.name as name_worksheet
            from
                collections
            left join
                worksheets on worksheets.id = collections.worksheet_id
            left join
                letter on letter.letter_id = collections.letter_id
            left join
                letter_detail on letter_detail.letter_detail_id = collections.letter_detail_id
            where
                collections.letter_id = $letter->LETTER_ID
            group by
                collections.letter_id,
                collections.title,
                collections.isbn,
                collections.mark_province,
                collections.mark_national,
                collections.branch_id,
                letter_detail.qty_accept,
                letter.accept_date,
                worksheets.name
        ");

        $htmlCollections = '<table border="1" style="font-size:8px">';
        $htmlCollections .= '<tr>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">No</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Tanggal Terima</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Judul</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Jenis Koleksi</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">ISBN/ISSN</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Jumlah (Eksemplar)</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">TRK</th>';
        $htmlCollections .= '</tr>';

        if ($collections) {
            foreach ($collections as $key => $c) {
                $TRKNo = ($c->BRANCH_ID == Main::IS_CENTER_BRANCH) ? $c->MARK_NATIONAL : $c->MARK_PROVINCE;
                $htmlCollections .= '<tr>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($key + 1) . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . date('d-m-Y', strtotime($c->ACCEPT_DATE_LETTER)) . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->TITLE ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->NAME_WORKSHEET ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->ISBN ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->QTY_ACCEPT_LETTER_DETAIL ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($TRKNo ?? '-') . '</td>';
                $htmlCollections .= '</tr>';
            }
        }

        $htmlCollections .= '</table>';

        $pdf->AddPage();
        $pdf->writeHTML($htmlCollections, true, false, true, false, '');

        $letterNumberUT = $letter->LETTER_NUMBER_UT ?? date('YmdHis');
        $filename = storage_path("app/public/delivery/list/receipt/$letterNumberUT.pdf");

        return $pdf->output($filename, 'I');
    }
}
