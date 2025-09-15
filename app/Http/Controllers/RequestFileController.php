<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class RequestFileController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'request-file'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_collection_requests.id',
            null,
            'penerbit.name',
            'catalogs.title',
            'e_collection_requests.status',
            'e_collection_requests.count_download',
            'e_collection_requests.request_letter',
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

        if ($request->executor_id) {
            $whereCondition[] = "catalogs.penerbit_id = $request->executor_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e_collection_requests.created_at >= date '$startDate' and e_collection_requests.created_at <= date '$endDate')";
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
                e_collection_requests
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_collection_requests
            join
                catalogs on catalogs.id = e_collection_requests.catalog_id
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
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
                                e_collection_requests.*,
                                penerbit.name as name_penerbit,
                                catalogs.title as title_catalog
                            from
                                e_collection_requests
                            join
                                catalogs on catalogs.id = e_collection_requests.catalog_id
                            left join
                                penerbit on penerbit.id = catalogs.penerbit_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                if ($val->STATUS == 1) {
                    $status = '
                        <a href="javascript:void(0);" class="link-primary fw-semibold d-inline-flex align-items-center dropdown-toggle ms-1" data-bs-toggle="dropdown">Tinjau</a>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0);" class="dropdown-item" onclick="setStatus(' . $val->ID . ', 2)">
                                <i class="ph-check me-2"></i>
                                Terima
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="setStatus(' . $val->ID . ', 3)">
                                <i class="ph-x me-2"></i>
                                Tolak
                            </a>
                        </div>
                    ';
                } else if ($val->STATUS == 2) {
                    $status = '
                        <span class="text-success fw-semibold">Diterima</span>
                    ';
                } else {
                    $status = '
                        <span class="text-danger fw-semibold">Ditolak</span>
                    ';
                }

                $letterRequest = '
                    <a href="' . url('stream-file?type=collection_request_letter&id=' . $val->ID . '&filename=' . $val->REQUEST_LETTER) . '" class="text-primary" target="_blank">
                        <i class="ph-file me-1"></i>
                        Lihat
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $val->NAME_PENERBIT,
                    $val->TITLE_CATALOG,
                    $status,
                    $val->COUNT_DOWNLOAD,
                    $letterRequest,
                    Carbon::parse($val->CREATED_AT)->format('d/m/Y'),
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

    public function setStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $data = QueryAPI::get("
            select
                e_collection_requests.*,
                penerbit.name as name_penerbit,
                penerbit.email1 as email_penerbit,
                catalogs.title as title_catalog
            from
                e_collection_requests
            join
                catalogs on catalogs.id = e_collection_requests.catalog_id
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            where
                e_collection_requests.id = $id
        ", true);

        $payload = [
            'approved_by' => session('id'),
            'status' => $status,
        ];

        if ($status == 2) {
            $tokenDownload = Str::random(40);
            $payload = array_merge($payload, [
                'token_download' => $tokenDownload,
                'expired_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            ]);

            if ($data) {
                $catalogId = $data->id ?? 0;
                $fileOriginal = QueryAPI::get("
                    select
                        *
                    from
                        catalogfiles
                    where
                        catalog_id = $catalogId
                ", true);

                if ($fileOriginal) {
                    $link = url('download/request-file?param=' . $catalogId . '&token=' . $tokenDownload);

                    $templateEmail = QueryAPI::get("
                        select
                            *
                        from
                            e_settings
                        where
                            slug = 'template-email-request-file-original'
                    ");

                    if ($templateEmail) {
                        $bodyParamEmail = [
                            'publisher' => $data->NAME_PENERBIT,
                            'title' => $data->TITLE_CATALOG,
                            'url' => $link,
                        ];

                        Mail::send([], [], function ($message) use ($data, $bodyParamEmail, $templateEmail) {
                            $message->to($data->EMAIL_PENERBIT, 'edeposit@perpusnas.go.id')
                                ->subject('Download File Original')
                                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                ->setBody(Main::parseTemplateEmail($bodyParamEmail, $templateEmail), 'text/html');
                        });
                    }
                }
            }
        }

        QueryAPI::update('e_collection_requests', $id, $payload);

        $response = [
            'code' => 200,
            'message' => 'Status telah diubah'
        ];

        return response()->json($response);
    }
}
