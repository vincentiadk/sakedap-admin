<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DataTableServersideController extends Controller
{
    private $credentialInlisIFrame;

    public function __construct()
    {
        $this->credentialInlisIFrame = Main::credentialInlisIFrame();
    }

    public function catalog(Request $request)
    {
        $column = [
            'c.id',
            null,
            'c.bibid',
            'c.title',
            'c.author',
            'p.name',
            'c.publishyear',
            'c.isbn',
            'c.callnumber',
            null,
            null,
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "((c.isdelete = 0 or c.isdelete is null) and (c.title is not null))";

        if (!Main::isSuperAdmin()) {
            $whereCondition[] = 'k.propinsiid = ' . session('province_id');
        }

        if ($request->worksheet_id) {
            $worksheetId = $request->worksheet_id ?? null;

            if (is_array($worksheetId)) {
                $worksheetId = implode(',', $worksheetId);
                $whereCondition[] = "c.worksheet_id in ($worksheetId)";
            } else {
                $whereCondition[] = "c.worksheet_id = $worksheetId";
            }
        }

        if ($request->searchable) {
            $whereConditionFilter = [];

            foreach ($request->searchable as $s) {
                $whereConditionFilter[] = "upper($s) like '%$search%'";
            }

            $whereCondition[] = '(' . implode(' or ', $whereConditionFilter) . ')';
        } else {
            if ($search) {
                $terms = [];
                $terms[] = "(upper(c.title) like '%$search%' or upper(p.name) like '%$search%')";

                $whereCondition[] = '(' . implode(' or ', $terms) . ')';
            }
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
                count(c.id) as total
            from
                catalogs c
            where
                (c.isdelete = 0 or c.isdelete is null)
                and c.title is not null
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(c.id) as total
            from
                catalogs c
            left join
                penerbit p on p.id = c.penerbit_id
            left join
                kabupaten k on k.id = c.city_id
            left join
                worksheets w on w.id = c.worksheet_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                rnum,
                c.id,
                c.bibid,
                c.title,
                c.author,
                c.publishyear,
                c.isbn,
                c.callnumber,
                p.name as name_penerbit,
                (
                    select
                        count(cl.id)
                    from
                        collections cl
                    where
                        cl.catalog_id = c.id
                ) as total_collection
            from (
                select
                    rownum as rnum,
                    t.*
                from (
                    select
                        c.id,
                        c.bibid,
                        c.title,
                        c.author,
                        c.publishyear,
                        c.isbn,
                        c.callnumber,
                        c.penerbit_id,
                        c.city_id,
                        c.worksheet_id
                    from
                        catalogs c
                    left join
                        penerbit p on p.id = c.penerbit_id
                    left join
                        kabupaten k on k.id = c.city_id
                    left join
                        worksheets w on w.id = c.worksheet_id
                    $whereClause
                    $orderBy
                ) t
                where
                    rownum <= $length
            ) c
            left join
                penerbit p on p.id = c.penerbit_id
            where
                c.rnum > $start
            $orderBy
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data select-btn" data-title="' . $val->TITLE . '" data-id="' . $val->ID . '">
                        Pilih
                    </a>
                ';

                $detailUrl = config('inlis.base_url') . "/KatalogDetailView.aspx?id=$val->ID&l=$this->credentialInlisIFrame";

                $detailPositionCenter = "var w = 900; var h = 550; var left = (screen.width / 2) - (w / 2); var top = (screen.height / 2) - (h / 2); window.open(this.href, 'DetailWindow', 'width=' + w + ',height=' + h + ',top=' + top + ',left=' + left + ',scrollbars=yes,resizable=yes'); return false;";

                $detail = '<a href="' . $detailUrl . '" class="text-primary detail-link" target="_blank" rel="noopener noreferrer" data-url="' . $detailUrl . '" onclick="' . $detailPositionCenter . '">Lihat</a>';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->BIBID,
                    $val->ISBN,
                    $val->CALLNUMBER,
                    $val->TOTAL_COLLECTION,
                    $val->PUBLISHYEAR,
                    $val->TITLE,
                    $val->NAME_PENERBIT,
                    $val->AUTHOR,
                    $detail,
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

    public function catalogParent(Request $request)
    {
        $column = [
            'c.id',
            null,
            'c.bibid',
            'c.title',
            'c.author',
            'p.name',
            'c.publishyear',
            'c.isbn',
            'c.callnumber',
            null,
            null,
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(c.isdelete = 0 or c.isdelete is null)";
        $whereCondition[] = "(c.title is not null)";
        $whereCondition[] = "(c.edeposit_col_id is not null and c.worksheet_id = 142)";

        if (!Main::isSuperAdmin()) {
            $whereCondition[] = 'k.propinsiid = ' . session('province_id');
        }

        if ($request->searchable) {
            $whereConditionFilter = [];

            foreach ($request->searchable as $s) {
                $whereConditionFilter[] = "upper($s) like '%$search%'";
            }

            $whereCondition[] = '(' . implode(' or ', $whereConditionFilter) . ')';
        } else {
            if ($search) {
                $terms = [];
                $terms[] = "(upper(c.title) like '%$search%' or upper(p.name) like '%$search%')";

                $whereCondition[] = '(' . implode(' or ', $terms) . ')';
            }
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
                count(c.id) as total
            from
                catalogs c
            where
                (c.isdelete = 0 or c.isdelete is null)
                and c.title is not null
                and c.edeposit_col_id is not null
                and c.worksheet_id = 142
                and exists (
                    select
                        1
                    from
                        e_collections ec
                    where
                        ec.id = c.edeposit_col_id
                )
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(c.id) as total
            from
                catalogs c
            left join
                penerbit p on p.id = c.penerbit_id
            left join
                kabupaten k on k.id = c.city_id
            left join
                worksheets w on w.id = c.worksheet_id
            inner join
                e_collections ec on ec.id = c.edeposit_col_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                rnum,
                c.id,
                c.bibid,
                c.title,
                c.author,
                c.publishyear,
                c.isbn,
                c.callnumber,
                p.name as name_penerbit,
                (
                    select
                        count(cl.id)
                    from
                        collections cl
                    where
                        cl.catalog_id = c.id
                ) as total_collection
            from (
                select
                    rownum as rnum,
                    t.*
                from (
                    select
                        c.id,
                        c.bibid,
                        c.title,
                        c.author,
                        c.publishyear,
                        c.isbn,
                        c.callnumber,
                        c.penerbit_id,
                        c.city_id,
                        c.worksheet_id,
                        c.edeposit_col_id
                    from
                        catalogs c
                    left join
                        penerbit p on p.id = c.penerbit_id
                    left join
                        kabupaten k on k.id = c.city_id
                    left join
                        worksheets w on w.id = c.worksheet_id
                    inner join
                        e_collections ec on ec.id = c.edeposit_col_id
                    $whereClause
                    $orderBy
                ) t
                where
                    rownum <= $length
            ) c
            left join
                penerbit p on p.id = c.penerbit_id
            where
                c.rnum > $start
            $orderBy
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data select-btn" data-title="' . $val->TITLE . '" data-id="' . $val->ID . '">
                        Pilih
                    </a>
                ';

                $detailUrl = config('inlis.base_url') . "/KatalogDetailView.aspx?id=$val->ID&l=$this->credentialInlisIFrame";

                $detailPositionCenter = "var w = 900; var h = 550; var left = (screen.width / 2) - (w / 2); var top = (screen.height / 2) - (h / 2); window.open(this.href, 'DetailWindow', 'width=' + w + ',height=' + h + ',top=' + top + ',left=' + left + ',scrollbars=yes,resizable=yes'); return false;";

                $detail = '<a href="' . $detailUrl . '" class="text-primary detail-link" target="_blank" rel="noopener noreferrer" data-url="' . $detailUrl . '" onclick="' . $detailPositionCenter . '">Lihat</a>';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->BIBID,
                    $val->ISBN,
                    $val->CALLNUMBER,
                    $val->TOTAL_COLLECTION,
                    $val->PUBLISHYEAR,
                    $val->TITLE,
                    $val->NAME_PENERBIT,
                    $val->AUTHOR,
                    $detail,
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

    public function catalogHistory(Request $request)
    {
        $column = [
            'historydata.id',
            'catalogs.title',
            'historydata.action',
            'historydata.actionby',
            'historydata.actiondate',
            'historydata.note',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $tableName = strtoupper($request->table);
        $idRef = $request->id;
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(UPPER(historydata.tablename) = '$tableName' and historydata.idref = '$idRef')";

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$search%'";
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
                historydata
            where
                (UPPER(tablename) = '$tableName' and idref = '$idRef')
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                historydata
            left join
                $tableName on $tableName.id = historydata.idref
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
                                historydata.*,
                                $tableName.title as title
                            from
                                historydata
                            left join
                                $tableName on $tableName.id = historydata.idref
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    $val->TITLE,
                    ucwords($val->ACTION),
                    $val->ACTIONBY,
                    Carbon::parse($val->ACTIONDATE)->isoFormat('dddd, D MMMM Y'),
                    '<code>' . $val->NOTE . '</code>',
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
}
