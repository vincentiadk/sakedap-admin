<?php

namespace App\Http\Controllers\CoachingSupervision;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'coaching-supervision.review',
                'plugins' => [
                    'datatable',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'penerbit.id',
            null,
            'penerbit.id',
            'penerbit.name',
            'penerbit.email1',
            'penerbit_kategori.name',
            'penerbit_jenis.name',
            'penerbit.telp1',
            'penerbit.createdate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "penerbit.status = '1'";

        if (Main::isNotCenterBranch()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
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
                penerbit
            where
                status = '1'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                penerbit
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
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
                                penerbit.*,
                                penerbit_kategori.name as name_penerbit_kategori,
                                penerbit_jenis.name as name_penerbit_jenis
                            from
                                penerbit
                            left join
                                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
                            left join
                                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
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
                    <div class="btn-group">
                        <button type="button" class="btn btn-flat-primary w-100 btn-sm fw-semibold dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="ph-hand-pointing me-1"></i>
                            Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showDataUpdate(' . $val->ID . ')">
                                <i class="ph-info me-1"></i>
                                Tinjau Data
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="destroyData(' . $val->ID . ')">
                                <i class="ph-trash-simple me-1"></i>
                                Hapus Data
                            </a>
                        </div>
                    </div>
                ';

                $email = '
                    <div>Utama : ' . $val->EMAIL1 . '</div>
                    <div>Alternatif : ' . $val->EMAIL2 . '</div>
                ';

                $phone = '
                    <div>Utama : ' . $val->TELP1 . '</div>
                    <div>Alternatif : ' . $val->TELP2 . '</div>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->ID,
                    $val->NAME,
                    $email,
                    $val->NAME_PENERBIT_KATEGORI,
                    $val->NAME_PENERBIT_JENIS,
                    $phone,
                    Carbon::parse($val->CREATEDATE)->isoFormat('dddd, D MMMM Y'),
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

    public function showData(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("
            select
                penerbit.*,
                penerbit_kategori.name as name_penerbit_kategori,
                penerbit_jenis.name as name_penerbit_jenis,
                parent.name as name_parent,
                propinsi.namapropinsi as namapropinsi,
                kabupaten.namakab as namakab,
                kecamatan.namakec as namakec,
                kelurahan.namakel as namakel
            from
                penerbit
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
            left join
                penerbit parent on parent.id = penerbit.parent_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            left join
                kabupaten on kabupaten.id = penerbit.city_id
            left join
                kecamatan on kecamatan.id = penerbit.district_id
            left join
                kelurahan on kelurahan.id = penerbit.village_id
            where
                penerbit.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $status = $request->status;

        try {
            $payload = [];

            if ($status == 3) {
                $payload = [
                    'status' => 3,
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ];
            } else {
                $payload = [
                    'status' => $status,
                    'keterangan' => $request->description,
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ];
            }

            QueryAPI::update('penerbit', $id, $payload, false);

            $response = [
                'code' => 200,
                'message' => 'Data telah direview'
            ];
        } catch (\Exception $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function destroyData(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::delete('penerbit', $id);

            $response = [
                'code' => 200,
                'message' => 'Data telah dihapus'
            ];
        } catch (\Exception $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }
}
