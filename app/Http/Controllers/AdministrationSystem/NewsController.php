<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'category' => QueryAPI::get("select * from e_news_kategori") ?? [],
                'content' => 'administration-system.news',
                'plugins' => [
                    'datatable',
                    'select2',
                    'summernote',
                    'lightbox',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_news.id',
            null,
            'e_news.image',
            'e_news_kategori.name',
            'e_news_kategori.content',
            'e_news.title',
            'e_news.lang',
            'e_news.lampiran_link',
            'e_news.status',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(e_news.deleted_at is null and e_news.flag = 'BERITA')";

        if ($request->category) {
            $whereCondition[] = "e_news.kategori_id = $request->category";
        }

        if ($request->ownership) {
            $filterPages = $request->ownership == 1 ? '> 0' : '< 1';
            $whereCondition[] = "instr(',' || e_news_kategori.content || ',', ',' || to_char(e_news.id) || ',') $filterPages";
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
                e_news
            where
                deleted_at is null and
                flag = 'BERITA'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_news
            left join
                e_news_kategori on e_news_kategori.id = e_news.kategori_id
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
                                e_news.*,
                                e_news_kategori.name as name_e_news_kategori,
                                e_news_kategori.content as content_e_news_kategori
                            from
                                e_news
                            left join
                                e_news_kategori on e_news_kategori.id = e_news.kategori_id
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
                            <a href="' . url('administration-system/news/preview/' . $val->ID) . '" class="dropdown-item">
                                <i class="ph-eye me-1"></i>
                                Preview
                            </a>
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
                        <a href="' . url('stream-file') . '?type=gambar_artikel&id=' . $val->ID . '&filename=' . $val->IMAGE . '" data-lightbox="news-' . $val->ID . '" data-title="' . $val->IMAGE . '" class="btn btn-success btn-sm">
                            <i class="ph-image me-1"></i>
                            Lihat Gambar
                        </a>
                    ';
                }

                $attachmentLink = '';

                if ($val->LAMPIRAN_LINK) {
                    $attachmentLink = '
                        <a href="' . $val->LAMPIRAN_LINK . '" class="text-primary" target="_blank">' . $val->LAMPIRAN_LINK . '</a>
                    ';
                }

                $explodeCategoryContent = explode(',', $val->CONTENT_E_NEWS_KATEGORI ?? '');

                $data[] = [
                    $start + 1,
                    $action,
                    $image,
                    $val->NAME_E_NEWS_KATEGORI,
                    in_array($val->ID, $explodeCategoryContent) ? '<i class="ph-check text-success"></i>' : '<i class="ph-x text-danger"></i>',
                    $val->TITLE,
                    $val->LANG,
                    $attachmentLink,
                    $val->STATUS,
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

    public function preview($id)
    {
        $news = QueryAPI::get("
            select
                e_news.*,
                e_news_kategori.name as name_e_news_kategori
            from
                e_news
            left join
                e_news_kategori on e_news_kategori.id = e_news.kategori_id
            where
                e_news.id = $id
        ", true);

        if (!$news) {
            abort(404);
        }

        return view('layouts.index', [
            'data' => [
                'news' => $news,
                'content' => 'administration-system.news-preview',
            ]
        ]);
    }

    public function createData(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'image' => 'required|image|mimes:png,jpg,jpeg|max:500',
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
        ], [
            'image.required' => 'Gambar tidak boleh kosong',
            'image.image' => 'Gambar tidak valid',
            'image.mimes' => 'Gambar harus png, jpg, jpeg',
            'image.max' => 'Gambar maksimal 500 KB',
            'title.required' => 'Judul tidak boleh kosong',
            'content.required' => 'Konten tidak boleh kosong',
            'category_id.required' => 'Kategori tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $createData = QueryAPI::create('e_news', [
                    'title' => $request->title,
                    'ringkasan' => $request->summary,
                    'slug' => Str::slug($request->title, '-'),
                    '!content' => $request->content,
                    'status' => $request->status,
                    'lampiran_link' => $request->attachment_link,
                    'kategori_id' => $request->category_id,
                    'flag' => 'BERITA',
                    'lang' => $request->lang,
                ]);

                if ($createData) {
                    $uploadFile = QueryAPI::uploadFile([
                        'type' => 'gambar_artikel',
                        'id' => $createData->ID,
                        'iszip' => 0,
                        'file' => $request->file('image'),
                    ]);

                    if ($uploadFile) {
                        QueryAPI::update('e_news', $createData->ID, [
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
               *
            from
                e_news
            where
                id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:500',
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
        ], [
            'image.image' => 'Gambar tidak valid',
            'image.mimes' => 'Gambar harus png, jpg, jpeg',
            'image.max' => 'Gambar maksimal 500 KB',
            'title.required' => 'Judul tidak boleh kosong',
            'content.required' => 'Konten tidak boleh kosong',
            'category_id.required' => 'Kategori tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $updateData = QueryAPI::update('e_news', $id, [
                    'title' => $request->title,
                    'ringkasan' => $request->summary,
                    'slug' => Str::slug($request->title, '-'),
                    '!content' => $request->content,
                    'status' => $request->status,
                    'lampiran_link' => $request->attachment_link,
                    'kategori_id' => $request->category_id,
                    'flag' => 'BERITA',
                    'lang' => $request->lang,
                ]);

                if ($updateData) {
                    if ($request->hasFile('image')) {
                        QueryAPI::removeFile([
                            'type' => 'gambar_artikel',
                            'id' => $id,
                            'filename' => $query->IMAGE ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'gambar_artikel',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('image'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_news', $id, [
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
            QueryAPI::update('e_news', $id, [
                'deleted_at' => date('Y-m-d H:i:s')
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
