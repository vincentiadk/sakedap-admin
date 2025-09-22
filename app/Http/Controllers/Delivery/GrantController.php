<?php

namespace App\Http\Controllers\Delivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GrantController extends Controller
{
    public function index()
    {
        $data = [
            'deliveryService' => QueryAPI::get("select * from jasa_pengiriman"),
            'content' => 'delivery.grant'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            null,
            'hibah_detail.judul',
            'hibah_detail.penerbit',
            'branchs.name',
            'jasa_pengiriman.name',
            'letter.receipt_no',
            'letter_detail.qty_hibah',
            'collectionsources.name',
            'hibah_group.code',
            'letter_detail.remark',
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

            $whereCondition[] = "(hibah_detail.createdate >= date '$startDate' and hibah_detail.createdate <= date '$endDate')";
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
                hibah_detail
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                hibah_detail
            left join
                collectionsources on collectionsources.id = hibah_detail.source_id
            left join
                hibah_group on hibah_group.id = hibah_detail.group_id
            left join
                letter_detail on letter_detail.letter_detail_id = hibah_detail.letter_detail_id
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
                                hibah_detail.*,
                                hibah_group.code as code_hibah_group,
                                collectionsources.name as name_collectionsource,
                                letter_detail.qty_hibah as qty_hibah_letter_detail,
                                letter_detail.remark as remark_letter_detail,
                                jasa_pengiriman.name as name_jasa_pengiriman,
                                penerbit.name as name_penerbit,
                                branchs.name as name_branch,
                                letter.receipt_no as receipt_no_letter,
                                letter.status as status_letter
                            from
                                hibah_detail
                            left join
                                collectionsources on collectionsources.id = hibah_detail.source_id
                            left join
                                hibah_group on hibah_group.id = hibah_detail.group_id
                            left join
                                letter_detail on letter_detail.letter_detail_id = hibah_detail.letter_detail_id
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
                $dataRemark = explode(';', $val->REMARK_LETTER_DETAIL ?? '');
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
                    <input type="hidden" name="data" data-id="' . $val->ID . '" data-title="' . $val->JUDUL . '" data-executor="' . $val->PENERBIT . '" data-qty-grant="' . $val->QTY_HIBAH_LETTER_DETAIL . '" data-receipt="' . $val->RECEIPT_NO_LETTER . '" data-group="' . $val->CODE_HIBAH_GROUP . '">
                ';

                $data[] = [
                    $inputHidden,
                    $start + 1,
                    $val->JUDUL,
                    $val->PENERBIT,
                    $val->NAME_BRANCH,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->RECEIPT_NO_LETTER,
                    $val->QTY_HIBAH_LETTER_DETAIL,
                    $val->NAME_COLLECTIONSOURCE,
                    $val->CODE_HIBAH_GROUP,
                    $remark,
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

    public function createGroup(Request $request)
    {
        $id = $request->id ?? [];
        $idImplode = implode(',', $id);

        $dataHibahDetail = QueryAPI::get("
            select
                hibah_detail.*,
                letter_detail.qty_hibah as qty_hibah_letter_detail,
                letter.sender as sender_letter
            from
                hibah_detail
            left join
                letter_detail on letter_detail.letter_detail_id = hibah_detail.letter_detail_id
            left join
                letter on letter.letter_id = letter_detail.letter_id
            where
                hibah_detail.id in ($idImplode)
        ");

        if ($dataHibahDetail) {
            $code = 'HBH-' . date('Ymd-His');

            foreach ($dataHibahDetail as $dhd) {
                $group = QueryAPI::create('hibah_group', [
                    'code' => $code,
                    'jumlah_item' => $dhd->QTY_HIBAH_LETTER_DETAIL,
                    'total_nilai' => $dhd->TOTAL_NILAI,
                    'pengirim' => $dhd->SENDER_LETTER,
                    'keterangan' => $dhd->KETERANGAN,
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);

                if ($group) {
                    QueryAPI::update('hibah_detail', $dhd->ID, [
                        'group_id' => $group->ID,
                        'updateby' => session('name'),
                        'updatedate' => date('Y-m-d H:i:s'),
                        'updateterminal' => $request->ip(),
                    ], false);
                }
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Koleksi berhasil menjadi grup'
        ]);
    }

    public function outGroup(Request $request)
    {
        $id = $request->id ?? [];
        $idImplode = implode(',', $id);

        $dataHibahDetail = QueryAPI::get("
            select
                *
            from
                hibah_detail
            where
                id in ($idImplode)
        ");

        if ($dataHibahDetail) {
            foreach ($dataHibahDetail as $dhd) {
                QueryAPI::update('hibah_detail', $dhd->ID, [
                    'group_id' => null,
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);

                QueryAPI::delete('hibah_group', $dhd->GROUP_ID);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Koleksi berhasil dikeluarkan dari grup tersebut'
        ]);
    }
}
