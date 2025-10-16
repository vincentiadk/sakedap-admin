<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StorageSpaceController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'locationLibrary' => QueryAPI::get("select * from location_library where isdelete = 0 or isdelete is null"),
                'content' => 'administration-system.storage-space',
                'plugins' => [
                    'datatable',
                    'select2',
                    'lightbox',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'l.id',
            null,
            'l.denah',
            null,
            null,
            'l.code',
            'l.name',
            'l.description',
            'll.name',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(l.isdelete = 0 or l.isdelete is null)";

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
                locations
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                locations l
            left join
                location_library ll on ll.id = l.locationlibrary_id
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
                                l.*,
                                ll.name as name_location_library,
                                (
                                    select
                                        count(c.id)
                                    from
                                        collections c
                                    where
                                        c.location_id = l.id
                                ) as total_collection,
                                (
                                    select
                                        count(ls.id)
                                    from
                                        location_shelf ls
                                    where
                                        ls.location_id = l.id
                                ) as total_rack
                            from
                                locations l
                            left join
                                location_library ll on ll.id = l.locationlibrary_id
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

                $image = '
                    <button type="button" class="btn btn-danger btn-sm no-click">
                        <i class="ph-x me-1"></i>
                        Tidak Ada Gambar
                    </button>
                ';

                if ($val->DENAH) {
                    $image = '
                        <a href="' . url('stream-file') . '?type=denah_lokasi&id=' . $val->ID . '&filename=' . $val->DENAH . '" data-lightbox="denah-' . $val->ID . '" data-title="' . $val->DENAH . '" class="btn btn-success btn-sm">
                            <i class="ph-image me-1"></i>
                            Lihat Gambar
                        </a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $image,
                    $val->TOTAL_COLLECTION,
                    $val->TOTAL_RACK,
                    $val->CODE,
                    $val->NAME,
                    $val->DESCRIPTION,
                    $val->NAME_LOCATION_LIBRARY,
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
            'code' => 'required',
            'name' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:500',
        ], [
            'code.required' => 'Kode tidak boleh kosong',
            'name.required' => 'Kode tidak boleh kosong',
            'image.image' => 'Gambar tidak valid',
            'image.mimes' => 'Gambar harus png, jpg, jpeg',
            'image.mimes' => 'Gambar maksimal 500 KB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $createData = QueryAPI::create('locations', [
                    'code' => $request->code,
                    'name' => $request->name,
                    'description' => $request->description,
                    'locationlibrary_id' => $request->location_library_id,
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);

                if ($createData) {
                    if ($request->hasFile('image')) {
                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'denah_lokasi',
                            'id' => $createData->ID,
                            'iszip' => 0,
                            'file' => $request->file('image'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('locations', $createData->ID, [
                                'denah' => $uploadFile->FileName
                            ], false);
                        }
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
                *
            from
                locations
            where
                id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $query = QueryAPI::get("select * from locations where id = $id");

        $validation = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:500',
        ], [
            'code.required' => 'Kode tidak boleh kosong',
            'name.required' => 'Kode tidak boleh kosong',
            'image.image' => 'Gambar tidak valid',
            'image.mimes' => 'Gambar harus png, jpg, jpeg',
            'image.mimes' => 'Gambar maksimal 500 KB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $updateData = QueryAPI::update('locations', $id, [
                    'code' => $request->code,
                    'name' => $request->name,
                    'description' => $request->description,
                    'locationlibrary_id' => $request->location_library_id,
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);

                if ($updateData) {
                    if ($request->hasFile('image')) {
                        QueryAPI::removeFile([
                            'type' => 'denah_lokasi',
                            'id' => $id,
                            'filename' => $query->TTD_FILE_NAME ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'denah_lokasi',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('image'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('locations', $id, [
                                'denah' => $uploadFile->FileName
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
            QueryAPI::update('locations', $id, [
                'isdelete' => 1,
            ], false);

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
