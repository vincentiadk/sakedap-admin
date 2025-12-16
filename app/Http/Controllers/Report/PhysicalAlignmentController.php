<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhysicalAlignmentController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category is not null") ?? [],
                'source' => QueryAPI::get("select * from collectionsources where (isdelete = 0 or isdelete is null)") ?? [],
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null)") ?? [],
                'category' => QueryAPI::get("select * from collectioncategorys where (isdelete = 0 or isdelete is null)") ?? [],
                'access' => QueryAPI::get("select * from collectionrules where (isdelete = 0 or isdelete is null)") ?? [],
                'location' => QueryAPI::get("select * from locations where (isdelete = 0 or isdelete is null)") ?? [],
                'rack' => QueryAPI::get("select * from location_shelf") ?? [],
                'carpet' => QueryAPI::get("select * from location_rugs") ?? [],
                'availability' => QueryAPI::get("select * from collectionstatus") ?? [],
                'content' => 'report.physical-alignment',
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
            'collections.id',
            'collections.item_number',
            'collections.noinduk',
            'collections.author',
            'collections.nomorpanggil',
            'worksheets.name',
            'collectionsources.name',
            'collectionmedias.name',
            'collectioncategorys.name',
            'collectionrules.name',
            'collections.status',
            'locations.name',
            'location_shelf.name',
            'location_rugs.name',
            'collections.shelving_date',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];

        if (Main::isNotSuperAdmin()) {
            $whereCondition[] = 'branchs.province_id = ' . session('province_id');
        }

        if ($request->worksheet_id) {
            $whereCondition[] = "collections.worksheet_id = $request->worksheet_id";
        }

        if ($request->source_id) {
            $whereCondition[] = "collections.source_id = '$request->source_id'";
        }

        if ($request->media_id) {
            $whereCondition[] = "collections.media_id = $request->media_id";
        }

        if ($request->category_id) {
            $whereCondition[] = "collections.category_id = $request->category_id";
        }

        if ($request->access_id) {
            $whereCondition[] = "collections.rule_id = $request->access_id";
        }

        if ($request->availability) {
            $whereCondition[] = "collections.status = $request->availability";
        }

        if ($request->location_id) {
            $whereCondition[] = "collections.location_id = $request->location_id";
        }

        if ($request->rack_id) {
            $whereCondition[] = "collections.location_shelf_id = $request->rack_id";
        }

        if ($request->carpet_id) {
            $whereCondition[] = "collections.location_rugs_id = $request->carpet_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(collections.shelving_date >= to_date('$startDate', 'YYYY-MM-DD') and collections.shelving_date < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

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
                collections
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                collections
            left join
                worksheets on worksheets.id = collections.worksheet_id
            left join
                branchs on branchs.id = collections.branch_id
            left join
                collectionsources on collectionsources.id = collections.source_id
            left join
                collectionmedias on collectionmedias.id = collections.media_id
            left join
                collectioncategorys on collectioncategorys.id = collections.category_id
            left join
                collectionrules on collectionrules.id = collections.rule_id
            left join
                locations on locations.id = collections.location_id
            left join
                location_shelf on location_shelf.id = collections.location_shelf_id
            left join
                location_rugs on location_rugs.id = collections.location_rugs_id
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
                                collections.id,
                                collections.item_number,
                                collections.noinduk,
                                collections.author,
                                collections.nomorpanggil,
                                collections.status,
                                collections.shelving_date,
                                worksheets.name as name_worksheet,
                                collectionsources.name as name_collectionsources,
                                collectionmedias.name as name_collectionmedias,
                                collectioncategorys.name as name_collectioncategorys,
                                collectionrules.name as name_collectionrules,
                                locations.name as name_location,
                                location_shelf.name as name_location_shelf,
                                location_rugs.name as name_location_rugs
                            from
                                collections
                            left join
                                worksheets on worksheets.id = collections.worksheet_id
                            left join
                                branchs on branchs.id = collections.branch_id
                            left join
                                collectionsources on collectionsources.id = collections.source_id
                            left join
                                collectionmedias on collectionmedias.id = collections.media_id
                            left join
                                collectioncategorys on collectioncategorys.id = collections.category_id
                            left join
                                collectionrules on collectionrules.id = collections.rule_id
                            left join
                                locations on locations.id = collections.location_id
                            left join
                                location_shelf on location_shelf.id = collections.location_shelf_id
                            left join
                                location_rugs on location_rugs.id = collections.location_rugs_id
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
                    $val->ITEM_NUMBER,
                    $val->NOINDUK,
                    $val->AUTHOR,
                    $val->NOMORPANGGIL,
                    $val->NAME_WORKSHEET,
                    $val->NAME_COLLECTIONMEDIAS,
                    $val->NAME_COLLECTIONCATEGORYS,
                    $val->NAME_COLLECTIONRULES,
                    $val->STATUS,
                    $val->NAME_LOCATION,
                    $val->NAME_LOCATION_SHELF,
                    $val->NAME_LOCATION_RUGS,
                    $val->SHELVING_DATE ? Carbon::parse($val->SHELVING_DATE)->isoFormat('dddd, D MMMM Y') : '',
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
