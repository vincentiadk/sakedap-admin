<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class IsrcController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Data ISRC',
            'content' => 'admin.isrc'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }
    /*public function datatable(Request $request) ini yang pakai SOLR
    {
        /*$where_like = [
            'id',
            'title',
            'producer_name',
            'composer_name',
            'isrc_number',
            'year',
            'asset_type',
            'publication_date'
        ];

        $data   = [];
        $offset = $request->start;
        $limit  = $request->length;
        $order  = $where_like[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        if($search) {
            array_push($data, [
                'title'         => '"' . $search . '"',
                'producer_name' => '"' . $search . '"',
                'composer_name' => '"' . $search . '"',
                'isrc_number'   => '"' . $search . '"'
            ]);
        }

        if($request->title) {
            array_push($data, ['title' => '"' . $request->title . '"']);
        }

        if($request->publisher_id) {
            $publisher      = DB::connection('isrc')->table('publishers')->where('id', $request->publisher_id)->first();
            $publisher_name = $publisher ? $publisher->name : null;

            array_push($data, ['producer_name' => '"' . $publisher_name . '"']);
        }

        if($request->publication_year) {
            array_push($data, ['year' => $request->publication_year]);
        }

        if($request->code) {
            array_push($data, ['isrc_number' => '"' . $request->code . '"']);
        }

        if($request->file_type) {
            array_push($data, ['asset_type' => $request->file_type]);
        }

        if($request->param) {
            if($request->param == 'annual') {
                $start  = $request->year_start . '-01-01T00:00:00Z';
                $finish = $request->year_end . '-12-31T23:59:59Z';
            } else if($request->param == 'monthly') {
                $start  = $request->month_year_start . '-' . $request->month_start . '-01T00:00:00Z';
                $finish = date('Y-m-t', strtotime($request->month_year_end . '-' . $request->month_end)) . 'T23:59:59Z';
            } else if($request->param == 'daily') {
                $start  = $request->day_start . 'T00:00:00Z';
                $finish = $request->day_end . 'T23:59:59Z';
            }

            array_push($data, ['publication_date' => "[$start TO $finish]"]);
        }

        $pagination = [
            'sort'   => $dir,
            'column' => $order,
            'offset' => $offset,
            'limit'  => $limit
        ];

        $datatable        = Solr::datatable('isrc', 'assets', Arr::collapse($data), $pagination);
        $response['data'] = [];
        $nomor            = $offset + 1;

        foreach($datatable['result'] as $d) {
            $title = $d['title'];
            $id    = $d['id'];

            $response['data'][] = [
                $nomor,
                '<a href="javascript:void(0);" onclick="openDetail(' . "'" . $id . "'" . ')">' . $title . '</a>',
                $d['producer_name'],
                $d['composer_name'],
                isset($d['isrc_number']) ? $d['isrc_number'] : '',
                $d['year'],
                $d['asset_type'],
                date('Y-m-d', strtotime($d['publication_date']))
            ];

            $nomor += 1;
        }


        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }
    */
    public function datatable(Request $request)
    {
        $whereLike = [
            'title',
            'isrc',
            'penerbit',
            'tipe',
            'penerbit',
            'tipe',
            'penerbit',
            'tipe',
        ];

        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $model = DB::connection('isrc')
            ->table('assets')
            ->join('producers', 'assets.producer_id', '=', 'producers.id')
            ->join('isrc_requests', 'assets.isrc_request_id', '=', 'isrc_requests.id')
            ->select('assets.*', 'producers.name as producer_name', 'isrc_requests.*')
            ->where(function ($query) use ($request) {
                if ($request->file_type) {
                    $query->where('asset_type', $request->file_type);
                }
                if ($request->title) {
                    $query->where('title', 'LIKE', "%{$request->title}%");
                }
                if ($request->publisher_id) {
                    $query->where('producer_id', $request->publisher_id);
                }
                if ($request->publication_year) {
                    $query->where('year', $request->publication_year);
                }

                if ($request->code) {
                    $query->where('isrc_number', 'like', "%{$request->code}%");
                }
                if ($request->param) {
                    if ($request->param == 'annual') {
                        $query->whereYear('isrc_requests.validation_date', '>=', $request->year_start)
                            ->whereYear('isrc_requests.validation_date', '<=', $request->year_end);
                    } else if ($request->param == 'monthly') {
                        $query->whereMonth('isrc_requests.validation_date', '>=', $request->month_start)
                            ->whereYear('isrc_requests.validation_date', '>=', $request->month_year_start)
                            ->whereMonth('isrc_requests.validation_date', '<=', $request->month_end)
                            ->whereYear('isrc_requests.validation_date', '<=', $request->month_year_start);
                    } else if ($request->param == 'daily') {
                        $query->whereDate('isrc_requests.validation_date', '>=', $request->day_start)
                            ->whereDate('isrc_requests.validation_date', '<=', $request->day_end);
                    }
                }
            })->where('isrc_requests.status', 'approved');


        $totalData = $model->count();
        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->latest('publication_date')
                ->get();
        } else {
            $totalFiltered = $model->where(function ($query) use ($search) {
                $query->where('producers.name', 'like', "%{$search}%")
                    ->orWhere('composer_name', 'like', "%{$search}%")
                    ->orWhere('isrc_number', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('asset_type', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            })
                ->count();
            $queryData = $model->where(function ($query) use ($search) {
                $query->where('producers.name', 'like', "%{$search}%")
                    ->orWhere('composer_name', 'like', "%{$search}%")
                    ->orWhere('isrc_number', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('asset_type', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->latest('isrc_requests.validation_date')
                ->get();
        }

        $response['data'] = [];

        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    "<a href='#' onclick='openDetail(id)' id='$val->id'>$val->title</a>",
                    $val->producer_name,
                    $val->composer_name,
                    $val->isrc_number,
                    $val->year,
                    $val->asset_type,
                    $val->validation_date,
                ];
                $nomor += 1;
            }
        }


        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function show(Request $request)
    {
        $data = DB::connection('isrc')
            ->table('assets')
            ->where('id', $request->id)
            ->first();

        if ($data) {
            $cover = $this->getFile($data->cover_path, $data->cover_mime_type);
            $file  = $this->getFile($data->file_path, $data->file_mime_type);
            $type  = $data->asset_type;
        } else {
            $type  = '';
            $file  = '';
            $cover = '';
        }

        return response()->json([
            'cover' => $cover,
            'file'  => $file,
            'type'  => $type
        ]);
    }

    private function getFile($path = null, $type)
    {
        #$domain       = 'https://isrc.perpusnas.go.id/';
        #$replace_str  = str_replace([DIRECTORY_SEPARATOR, '//', "\\"], '/', $path);
        #$path_primary = ['D:/isrc'];
        #$file         = str_replace($path_primary, $domain, $replace_str);

        #if($path) {
        #    if(file_get_contents($file) !== false) {
        #        return $file;
        #    }
        #}

        if ($path) {
            return "data:$type;base64," . base64_encode(file_get_contents($path));
        }

        #return '';
    }
}
