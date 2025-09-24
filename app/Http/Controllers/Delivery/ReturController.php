<?php

namespace App\Http\Controllers\Delivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReturController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman"),
                'content' => 'delivery.retur',
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
            null,
            'letter_detail.letter_detail_id',
            null,
            'letter_detail.title',
            'penerbit.name',
            'branchs.name',
            'jasa_pengiriman.name',
            'letter.receipt_no',
            'letter_detail.qty_retur',
            'letter_detail.jenis_media',
            'letter_detail.remark',
            'letter.proses_by',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "letter.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL')";
        $whereCondition[] = "letter_detail.qty_retur > 0";

        if (Main::isNotCenterBranch()) {
            $whereCondition[] = 'branchs.province_id = ' . session('province_id');
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "letter.jasa_pengiriman_id = $request->delivery_service_id";
        }

        if ($request->executor_id) {
            $whereCondition[] = "letter.penerbit_id = $request->executor_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(letter.$request->date_type >= to_date('$startDate', 'YYYY-MM-DD') and letter.$request->date_type < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                letter_detail
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                letter_detail
            left join
                letter on letter.letter_id = letter_detail.letter_id
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
            left join
                branchs on branchs.id = letter.branch_id
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
                                letter_detail.*,
                                jasa_pengiriman.name as name_jasa_pengiriman,
                                penerbit.name as name_penerbit,
                                branchs.name as name_branch,
                                letter.receipt_no as receipt_no_letter,
                                letter.status as status_letter,
                                letter.proses_by as proses_by_letter
                            from
                                letter_detail
                            left join
                                letter on letter.letter_id = letter_detail.letter_id
                            left join
                                penerbit on penerbit.id = letter.penerbit_id
                            left join
                                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
                            left join
                                branchs on branchs.id = letter.branch_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="grant(' . $val->LETTER_DETAIL_ID . ')">
                        <i class="ph-gift me-1"></i>
                        Hibahkan
                    </a>
                ';

                $dataRemark = explode(';', $val->REMARK ?? '');
                $listRemark = '';

                if ($dataRemark) {
                    foreach ($dataRemark as $key => $dr) {
                        $listRemark .= '<div>' . $key + 1 . '. ' . $dr . '</div>';
                    }
                }

                $remark = '
                    <button type="button" class="btn btn-light btn-sm" onclick="onPopover(this, ' . "'$listRemark'" . ')">Lihat</button>
                ';

                $inputHidden = '
                    <input type="hidden" name="data" data-id="' . $val->LETTER_DETAIL_ID . '" data-title="' . $val->TITLE . '" data-executor="' . $val->NAME_PENERBIT . '" data-qty-retur="' . $val->QTY_RETUR . '" data-receipt="' . $val->RECEIPT_NO_LETTER . '">
                ';

                $data[] = [
                    $inputHidden,
                    $start + 1,
                    $action,
                    $val->TITLE,
                    $val->NAME_PENERBIT,
                    $val->NAME_BRANCH,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->RECEIPT_NO_LETTER,
                    $val->QTY_RETUR,
                    $val->JENIS_MEDIA,
                    $remark,
                    $val->PROSES_BY_LETTER,
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

    public function grant(Request $request)
    {
        $id = $request->id ?? [];
        $idImplode = implode(',', $id);

        $dataLetterDetail = QueryAPI::get("
            select
                *
            from
                letter_detail
            where
                letter_detail_id in ($idImplode)
        ");

        if ($dataLetterDetail) {
            foreach ($dataLetterDetail as $dld) {
                QueryAPI::update('letter_detail', $dld->LETTER_DETAIL_ID, [
                    'qty_hibah' => $dld->QTY_REJECT,
                    'qty_retur' => null,
                ], false);

                QueryAPI::create('hibah_detail', [
                    'judul' => $dld->TITLE,
                    'penerbit' => $dld->PUBLISHER,
                    'isbn' => $dld->ISBN,
                    'tahun_terbit' => $dld->PUBLISH_YEAR,
                    'jumlah_eksemplar' => $dld->QTY_REJECT,
                    'harga' => $dld->PRICE,
                    'total_nilai' => (float) ($dld->PRICE ?? 0) * (float) ($dld->QTY_REJECT ?? 0),
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'deskripsi_fisik' => $dld->DESKRIPSIFISIK,
                    'jenis_isi' => $dld->JENIS_ISI,
                    'jenis_wadah' => $dld->JENIS_WADAH,
                    'jenis_media' => $dld->JENIS_MEDIA,
                    'source_id' => 6,
                    'source_sub_id' => 3,
                    'ketersediaan_id' => 1,
                    'partner_id' => 9687,
                    'kala_terbit' => $dld->KALA_TERBIT,
                    'letter_detail_id' => $dld->LETTER_DETAIL_ID,
                ], false);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Koleksi berhasil dihibahkan'
        ]);
    }
}
