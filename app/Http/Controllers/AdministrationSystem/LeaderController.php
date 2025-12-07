<?php

namespace App\Http\Controllers\AdministrationSystem;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LeaderController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from branchs where isdelete = 0 or isdelete is null") ?? [],
                'content' => 'administration-system.leader',
                'plugins' => [
                    'ckeditor',
                    'select2',
                    'datatable',
                    'lightbox',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'penanggung_jawab.id',
            null,
            'penanggung_jawab.ttd_file_name',
            'propinsi.name',
            'penanggung_jawab.nama',
            'penanggung_jawab.nip',
            'branchs.name',
            'penanggung_jawab.jabatan',
            'penanggung_jawab.tanggal_awal',
            'penanggung_jawab.tanggal_akhir',
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
                penanggung_jawab
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                penanggung_jawab
            left join
                branchs on branchs.id = penanggung_jawab.branch_id
            left join
                propinsi on propinsi.id = branchs.province_id
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
                                penanggung_jawab.*,
                                branchs.name as name_branch,
                                propinsi.namapropinsi as namapropinsi
                            from
                                penanggung_jawab
                            left join
                                branchs on branchs.id = penanggung_jawab.branch_id
                            left join
                                propinsi on propinsi.id = branchs.province_id
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
                                <i class="ph-pen me-1"></i>
                                Ubah Data
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="destroyData(' . $val->ID . ')">
                                <i class="ph-trash-simple me-1"></i>
                                Hapus Data
                            </a>
                        </div>
                    </div>
                ';

                $signature = '
                    <button type="button" class="btn btn-danger btn-sm no-click">
                        <i class="ph-x me-1"></i>
                        Tidak Ada Gambar
                    </button>
                ';

                if ($val->TTD_FILE_NAME) {
                    $signature = '
                        <a href="' . url('stream-file') . '?type=gambar_ttd&id=' . $val->ID . '&filename=' . $val->TTD_FILE_NAME . '" data-lightbox="ttd-' . $val->ID . '" data-title="' . $val->TTD_FILE_NAME . '" class="btn btn-success btn-sm">
                            <i class="ph-image me-1"></i>
                            Lihat Gambar
                        </a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $signature,
                    $val->NAMAPROPINSI,
                    $val->NAMA,
                    $val->NIP,
                    $val->NAME_BRANCH,
                    $val->JABATAN,
                    Carbon::parse($val->TANGGAL_AWAL)->isoFormat('dddd, D MMMM Y'),
                    Carbon::parse($val->TANGGAL_AKHIR)->isoFormat('dddd, D MMMM Y'),
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

    public function createData(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required',
            'position' => 'required',
            'start_date' => 'required',
            'branch_id' => 'required',
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:500',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'number.required' => 'NIP tidak boleh kosong',
            'position.required' => 'Jabatan tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai menjabat tidak boleh kosong',
            'branch_id.required' => 'Perpustakaan tidak boleh kosong',
            'signature.required' => 'Gambar TTD tidak boleh kosong',
            'signature.image' => 'Gambar TTD tidak valid',
            'signature.mimes' => 'Gambar TTD harus png, jpg, jpeg',
            'signature.mimes' => 'Gambar TTD maksimal 500 KB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $createData = QueryAPI::create('penanggung_jawab', [
                    'nama' => $request->name,
                    'nip' => $request->number,
                    'jabatan' => $request->position,
                    'tanggal_awal' => $request->start_date,
                    'tanggal_akhir' => $request->end_date,
                    'createby' => session('username'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'branch_id' => $request->branch_id,
                ], false);

                if ($createData) {
                    $uploadFile = QueryAPI::uploadFile([
                        'type' => 'gambar_ttd',
                        'id' => $createData->ID,
                        'iszip' => 0,
                        'file' => $request->file('signature'),
                    ]);

                    if ($uploadFile) {
                        QueryAPI::update('penanggung_jawab', $createData->ID, [
                            'ttd_file_name' => $uploadFile->FileName
                        ], false);
                    }
                }

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
        $data = QueryAPI::get("
            select
                penanggung_jawab.*,
                branchs.name as name_branch
            from
                penanggung_jawab
            left join
                branchs on branchs.id = penanggung_jawab.branch_id
            where
                penanggung_jawab.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $query = QueryAPI::get("select * from penanggung_jawab where id = $id", true);

        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required',
            'position' => 'required',
            'start_date' => 'required',
            'branch_id' => 'required',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:500',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'number.required' => 'NIP tidak boleh kosong',
            'position.required' => 'Jabatan tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai menjabat tidak boleh kosong',
            'branch_id.required' => 'Perpustakaan tidak boleh kosong',
            'signature.image' => 'Gambar TTD tidak valid',
            'signature.mimes' => 'Gambar TTD harus png, jpg, jpeg',
            'signature.mimes' => 'Gambar TTD maksimal 500 KB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $updateData = QueryAPI::update('penanggung_jawab', $id, [
                    'nama' => $request->name,
                    'nip' => $request->number,
                    'jabatan' => $request->position,
                    'tanggal_awal' => $request->start_date,
                    'tanggal_akhir' => $request->end_date,
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'branch_id' => $request->branch_id,
                ], false);

                if ($updateData) {
                    if ($request->hasFile('signature')) {
                        QueryAPI::removeFile([
                            'type' => 'gambar_ttd',
                            'id' => $id,
                            'filename' => $query->TTD_FILE_NAME ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'gambar_ttd',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('signature'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('penanggung_jawab', $id, [
                                'ttd_file_name' => $uploadFile->FileName
                            ], false);
                        }
                    }
                }

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

        try {
            QueryAPI::delete('penanggung_jawab', $id);

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
