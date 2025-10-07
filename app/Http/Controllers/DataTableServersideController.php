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
            'catalogs.id',
            null,
            'catalogs.bibid',
            'catalogs.title',
            'catalogs.author',
            'penerbit.name',
            'catalogs.publishyear',
            'catalogs.isbn',
            'catalogs.callnumber',
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
        $whereCondition[] = "(catalogs.isdelete = 0 or catalogs.isdelete is null) and (catalogs.title is not null)";

        if (Main::isNotCenterBranch()) {
            $whereCondition[] = 'kabupaten.propinsiid = ' . session('province_id');
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

                foreach ($column as $c) {
                    if ($c) {
                        $terms[] = "upper($c) like '%$search%'";
                    }
                }

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
                count(*) as total
            from
                catalogs
            where
                (
                    isdelete = 0 or
                    isdelete is null
                )
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                catalogs
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            left join
                kabupaten on kabupaten.id = catalogs.city_id
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
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
                                catalogs.id,
                                catalogs.bibid,
                                catalogs.title,
                                catalogs.author,
                                catalogs.publishyear,
                                catalogs.isbn,
                                catalogs.callnumber,
                                penerbit.name as name_penerbit,
                                coalesce(count(collections.id), 0) as total_collection
                            from
                                catalogs
                            left join
                                penerbit on penerbit.id = catalogs.penerbit_id
                            left join
                                kabupaten on kabupaten.id = catalogs.city_id
                            left join
                                worksheets on worksheets.id = catalogs.worksheet_id
                            left join
                                collections on collections.catalog_id = catalogs.id
                            $whereClause
                            group by
                                catalogs.id,
                                catalogs.bibid,
                                catalogs.title,
                                catalogs.author,
                                catalogs.publishyear,
                                catalogs.isbn,
                                catalogs.callnumber,
                                penerbit.name
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data select-btn" data-title="' . $val->TITLE . '" data-id="' . $val->ID . '">
                        Pilih
                    </a>
                ';

                $detailUrl = "https://digitlib.site/inlis-ent-2025/KatalogDetailView.aspx?id=$val->ID&l=$this->credentialInlisIFrame";

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
        $tableName = $request->table;
        $idRef = $request->id;
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(historydata.tablename = '$tableName' and historydata.idref = '$idRef')";

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
                (tablename = '$tableName' and idref = '$idRef')
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
                )
            where
                rnum > $start and rnum <= $length
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
