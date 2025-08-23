<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'master-data.visit'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable()
    {
        $data = QueryAPI::get("
            select
                *
            from
                e_kunjungan
        ");

        return DataTables::of(collect($data))
            ->addColumn('action', function ($query) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-flat-primary w-100 btn-sm fw-semibold dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="ph-hand-pointing me-1"></i>
                            Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0);" class="dropdown-item fs-13" onclick="showDataUpdate(' . $query->ID . ')">
                                <i class="ph-pen me-1"></i>
                                Ubah Data
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item fs-13" onclick="destroyData(' . $query->ID . ')">
                                <i class="ph-trash-simple me-1"></i>
                                Hapus Data
                            </a>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->escapeColumns()
            ->smart(true)
            ->toJson();
    }

    public function createData(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong'
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_kunjungan', [
                    'name' => $request->name,
                ]);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Membuat data kunjungan',
                    'causer_id' => session('id'),
                    'properties' => json_encode(['nama' => $request->name]),
                ]);

                $response = [
                    'code' => 200,
                    'message' => 'Data telah ditambahkan'
                ];
            } catch (\Exception $e) {
                $response = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json($response);
    }

    public function showData(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("select * from e_kunjungan where id = $id", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'name' => 'required',
        ], [
            'name.required' => 'nama agama tidak boleh kosong'
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_kunjungan', $id, [
                    'name' => $request->name
                ], true, true);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Mengubah data kunjungan',
                    'causer_id' => session('id'),
                    'properties' => json_encode(['nama' => $request->name]),
                ]);

                $response = [
                    'code' => 200,
                    'message' => 'Data telah diubah'
                ];
            } catch (\Exception $e) {
                $response = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json($response);
    }

    public function destroyData(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("select * from e_kunjungan where id = $id", true);

        try {
            QueryAPI::delete('e_kunjungan', $id);

            QueryAPI::activityLog([
                'log_name' => 'default',
                'description' => 'Menghapus data kunjungan',
                'causer_id' => session('id'),
                'properties' => json_encode(['nama' => $data->NAME ?? null]),
            ]);

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
