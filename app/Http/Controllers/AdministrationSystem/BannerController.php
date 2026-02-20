<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.banner',
                'plugins' => [
                    'datatable',
                    'lightbox',
                    'select2',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_banners.id',
            null,
            'e_banners.image',
            'e_promo.judul',
            'e_banners.title',
            'e_banners.description',
            'e_banners.type',
            'e_banners.status',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];

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
                e_banners
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_banners
            left join
                e_promo on e_promo.id = e_banners.promo_id
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
                                e_banners.*,
                                e_promo.judul as judul_e_promo
                            from
                                e_banners
                            left join
                                e_promo on e_promo.id = e_banners.promo_id
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

                if ($val->IMAGE) {
                    $image = '
                        <a href="' . url('stream-file') . '?type=gambar_banner&id=' . $val->ID . '&filename=' . $val->IMAGE . '" data-lightbox="banner-' . $val->ID . '" data-title="' . $val->IMAGE . '" class="btn btn-success btn-sm">
                            <i class="ph-image me-1"></i>
                            Lihat Gambar
                        </a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $image,
                    $val->JUDUL_E_PROMO,
                    $val->TITLE,
                    $val->DESCRIPTION,
                    ucwords($val->TYPE),
                    $val->STATUS == 1 ? 'Aktif' : 'Tidak Aktif',
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
            'title' => 'required',
            'type' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:500',
        ], [
            'title.required' => 'Judul tidak boleh kosong',
            'type.required' => 'Jenis tidak boleh kosong',
            'image.required' => 'Gambar tidak boleh kosong',
            'image.image' => 'Gambar tidak valid',
            'image.mimes' => 'Gambar harus png, jpg, jpeg',
            'image.max' => 'Gambar maksimal 500 KB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $createData = QueryAPI::create('e_banners', [
                    'title' => $request->title,
                    'description' => $request->description,
                    'type' => $request->type,
                    'status' => $request->status,
                    'promo_id' => $request->promotion_id,
                ]);

                if ($createData) {
                    $uploadFile = QueryAPI::uploadFile([
                        'type' => 'gambar_banner',
                        'id' => $createData->ID,
                        'iszip' => 0,
                        'file' => $request->file('image'),
                    ]);

                    if ($uploadFile) {
                        QueryAPI::update('e_banners', $createData->ID, [
                            'image' => $uploadFile->FileName
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
                e_banners.*,
                e_promo.judul as judul_e_promo
            from
                e_banners
            left join
                e_promo on e_promo.id = e_banners.promo_id
            where
                e_banners.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $query = QueryAPI::get("select * from e_banners where id = $id", true);

        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:500',
        ], [
            'title.required' => 'Judul tidak boleh kosong',
            'type.required' => 'Jenis tidak boleh kosong',
            'image.image' => 'Gambar tidak valid',
            'image.mimes' => 'Gambar harus png, jpg, jpeg',
            'image.max' => 'Gambar maksimal 500 KB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $updateData = QueryAPI::update('e_banners', $id, [
                    'title' => $request->title,
                    'description' => $request->description,
                    'type' => $request->type,
                    'status' => $request->status,
                    'promo_id' => $request->promotion_id,
                ]);

                if ($updateData) {
                    if ($request->hasFile('image')) {
                        QueryAPI::removeFile([
                            'type' => 'gambar_banner',
                            'id' => $id,
                            'filename' => $query->TTD_FILE_NAME ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'gambar_banner',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('image'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_banners', $id, [
                                'image' => $uploadFile->FileName
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
            QueryAPI::delete('e_banners', $id);

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
