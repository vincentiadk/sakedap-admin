<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DataTableServersideController extends Controller
{
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
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(catalogs.isdelete = 0 or catalogs.isdelete is null)";

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
                                penerbit.name as name_penerbit
                            from
                                catalogs
                            left join
                                penerbit on penerbit.id = catalogs.penerbit_id
                            left join
                                kabupaten on kabupaten.id = catalogs.city_id
                            left join
                                worksheets on worksheets.id = catalogs.worksheet_id
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
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm data select-btn" data-title="' . $val->TITLE . '" data-id="' . $val->ID . '">
                        Pilih
                    </a>
                ';

                $totalCollection = QueryAPI::get("
                    select
                        count(id)
                    from
                        collections
                    where
                        catalog_id = $val->ID
                ", true)->TOTAL ?? 0;

                $data[] = [
                    $start + 1,
                    $action,
                    $val->BIBID,
                    $val->ISBN,
                    $val->CALLNUMBER,
                    $totalCollection,
                    $val->PUBLISHYEAR,
                    $val->TITLE,
                    $val->NAME_PENERBIT,
                    $val->AUTHOR,
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
