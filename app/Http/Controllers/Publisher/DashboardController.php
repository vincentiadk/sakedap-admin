<?php

namespace App\Http\Controllers\Publisher;

use App\Models\Collection;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
use App\Models\DepositHead;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{

    public function index()
    {
        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;
        // $total_collection = [
        //     'book'     => Collection::where('type', 1)
        //                     ->where('publisher_id', $publisher_id)->count(),
        //     'partitur' => Collection::where('type', 2)
        //                     ->where('publisher_id', $publisher_id)->count(),
        //     'map'      => Collection::where('type', 3)
        //                     ->where('publisher_id', $publisher_id)->count(),
        //     'serial'   => Collection::where('type', 4)
        //                     ->where('publisher_id', $publisher_id)->count(),
        //     'audio'    => Collection::where('type', 5)
        //                     ->where('publisher_id', $publisher_id)->count(),
        //     'film'     => Collection::where('type', 6)
        //                     ->where('publisher_id', $publisher_id)->count()
        // ];


        // dd($not_delivered);

        // $date_before         = date('Y-m-d', strtotime('-10 days'));
        // $date_now            = date('Y-m-d');
        // $where_raw           = "DATE(created_at) >= '$date_before' AND DATE(created_at) <= '$date_now'";
        // $collection_last_day = [
        //     'book'     => Collection::where('type', 1)
        //         ->where('publisher_id', $publisher_id)
        //         ->whereRaw($where_raw)->count(),
        //     'partitur' => Collection::where('type', 2)
        //         ->where('publisher_id', $publisher_id)
        //         ->whereRaw($where_raw)->count(),
        //     'peta'     => Collection::where('type', 3)
        //         ->where('publisher_id', $publisher_id)
        //         ->whereRaw($where_raw)->count(),
        //     'serial'   => Collection::where('type', 4)
        //         ->where('publisher_id', $publisher_id)
        //         ->whereRaw($where_raw)->count(),
        //     'audio'    => Collection::where('type', 5)
        //         ->where('publisher_id', $publisher_id)
        //         ->whereRaw($where_raw)->count(),
        //     'film'     => Collection::where('type', 6)
        //         ->where('publisher_id', $publisher_id)
        //         ->whereRaw($where_raw)->count(),
        // ];

        // $file_type = [
        //     'pdf'  => CollectionMedia::where('extension', 'LIKE', '%pdf%')
        //         ->rightJoin('collections', 'collections.id', 'collection_media.collection_id')
        //         ->where('collections.publisher_id', $publisher_id)
        //         ->count(),
        //     'wav'  => CollectionMedia::where('extension', 'LIKE', '%wav%')
        //         ->rightJoin('collections', 'collections.id', 'collection_media.collection_id')
        //         ->where('collections.publisher_id', $publisher_id)
        //         ->count(),
        //     'mpeg' => CollectionMedia::where('extension', 'LIKE', '%mpeg%')
        //         ->rightJoin('collections', 'collections.id', 'collection_media.collection_id')
        //         ->where('collections.publisher_id', $publisher_id)
        //         ->count()
        // ];

        $data = [
            'title'                 => 'Dashboard',
            // 'collection_accept'     => Collection::where('status', 2)
            //     ->where('publisher_id', $publisher_id)
            //     ->latest('received_at')
            //     ->limit(8)->get(),
            // 'total_collection'      => $new_data,
            // 'activity'              => ActivityLog::where('causer_id', $user->id)->limit(8)->latest()->get(),
            // 'file_type'             => $file_type,
            // 'collection'            => $collection,
            // 'collection_last_day'   => $collection_last_day,
            'publisher_id'          => $publisher_id,
            // 'not_delivered'          => $not_delivered,
            'content'               => 'publisher.dashboard'
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function statistic($for, $param = null)
    {

        // dd($for);
        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;
        if ($for == 'collection_grouped') {
            $getCache = Cache::remember('collection_grouped', 600, function () use ($publisher_id) {
                $data =  DepositHead::selectRaw('count(collections.id) as total_collections, deposit_head.code, deposit_head.shape, deposit_head.category')
                    ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
                    ->where('collections.publisher_id', $publisher_id)
                    ->groupBy('deposit_head.category', 'deposit_head.code')
                    ->orderBy('total_collections', 'desc')
                    ->get();

                $new_data = [];
                foreach ($data as $value) {
                    $new_data['grouped'][$value['category']][$value['code']] = $value['total_collections'];
                    if (isset($new_data['total'][$value['category']])) {
                        $new_data['total'][$value['category']] += $value['total_collections'];
                    } else {
                        $new_data['total'][$value['category']] = $value['total_collections'];
                    }
                }

                $depositHead = DepositHead::all()->toArray();

                foreach ($depositHead as $value) {
                    if (!isset($new_data['grouped'][$value['category']][$value['code']])) {
                        $new_data['grouped'][$value['category']][$value['code']] = 0;
                    }

                    if (!isset($new_data['total'][$value['category']])) {
                        $new_data['total'][$value['category']] = 0;
                    }
                }

                $labels = [];
                foreach ($new_data['grouped'] as $k => $v) {
                    $labels[$k]['labels'] = array_keys($new_data['grouped'][$k]);
                    $datasets['label'] = 'Koleksi';
                    $datasets['data'] = array_values($new_data['grouped'][$k]);
                    $datasets['backgroundColor'] = $this->generateColor(array_values($new_data['grouped'][$k]));
                    $labels[$k]['datasets'][] = $datasets;
                }

                $labels['total']['labels'] = array_keys($new_data['total']);
                $datasets['label'] = 'Koleksi';
                $datasets['data'] = array_values($new_data['total']);
                $datasets['backgroundColor'] = $this->generateColor(array_values($new_data['total']));
                $labels['total']['datasets'][] = $datasets;
                return $labels;
            });
            return response()->json($getCache);
        } else if ($for == 'not_delivered') {
            $not_delivered = Collection::selectRaw('COUNT(CASE WHEN mark_national IS NULL THEN 1 END) AS total_national, COUNT(CASE WHEN mark_province IS NULL THEN 1 END) AS total_province')
                ->where('collections.publisher_id', $publisher_id)
                ->orderBy('total_national', 'desc')
                ->orderBy('total_province', 'desc')
                ->first();
            if ($not_delivered) {
                return response()->json([
                    [
                        'total' => $not_delivered->total_national,
                        'jenis' => 'Perpusnas',
                    ],
                    [
                        'total' => $not_delivered->total_province,
                        'jenis' => 'Perpustakan Provinsi',
                    ]
                ]);
            } else {
                return response()->json([
                    [
                        'total' => 0,
                        'jenis' => 'Perpusnas',
                    ],
                    [
                        'total' => 0,
                        'jenis' => 'Perpustakan Provinsi',
                    ]
                ]);
            }
        } else if ($for == 'total_koleksi') {
            $getCache = Cache::remember('total_koleksi', 600, function () use ($publisher_id) {
                $data =  DepositHead::selectRaw('
                    COUNT(CASE WHEN collections.status = "1" THEN 1 END) AS review, 
                    COUNT(CASE WHEN collections.status = "2" THEN 1 END) AS diterima, 
                    COUNT(CASE WHEN collections.status = "3" THEN 1 END) AS masalah, 
                    deposit_head.id as deposit_head_id,
                    deposit_head.shape,
                    deposit_head.is_serial
                ')
                    ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
                    ->where('collections.publisher_id', $publisher_id)
                    ->groupBy('deposit_head.id')
                    ->orderBy('deposit_head.id', 'desc')
                    ->get();

                $new_data = [];
                foreach ($data as $value) {
                    $new_data[$value['deposit_head_id']] = $value;
                }

                $depositHead = DepositHead::all()->toArray();

                foreach ($depositHead as $value) {
                    if (!isset($new_data[$value['id']])) {
                        $new_data[$value['id']]['review'] = 0;
                        $new_data[$value['id']]['diterima'] = 0;
                        $new_data[$value['id']]['masalah'] = 0;
                        $new_data[$value['id']]['deposit_head_id'] = $value['id'];
                        $new_data[$value['id']]['shape'] = $value['shape'];
                        $new_data[$value['id']]['is_serial'] = $value['is_serial'];
                    }
                }
                return $new_data;
            });
            return response()->json($getCache);
        } else if ($for == 'collection_status') {
            $getCache = Cache::remember('collection_status', 600, function () use ($publisher_id) {
                $getData =  Collection::selectRaw('
                    COUNT(CASE WHEN collections.status = "1" THEN 1 END) AS collection_accept, 
                    COUNT(CASE WHEN collections.status = "2" THEN 1 END) AS collection_review, 
                    COUNT(CASE WHEN collections.status = "3" THEN 1 END) AS collection_problem
                ')
                    ->where('collections.publisher_id', $publisher_id)
                    ->get();

                $data['labels'] = ['Diterima', 'Review', 'Bermasalah'];
                $datasets['label'] = 'Koleksi';
                $datasets['data'] = [
                    $getData[0]->collection_accept,
                    $getData[0]->collection_review,
                    $getData[0]->collection_problem,
                ];
                $datasets['backgroundColor'] = $this->generateColor($datasets['data']);
                $data['datasets'][] = $datasets;

                return $data;
            });
            return response()->json($getCache);
        } else if ($for == 'collection_accept') {
            $getCache = Cache::remember('collection_accept', 600, function () use ($publisher_id) {
                $data = Collection::where('status', 2)
                    ->where('publisher_id', $publisher_id)
                    ->latest('received_at')
                    ->limit(8)->get();

                $new_data = [];
                foreach ($data as $key => $value) {
                    $new_data[$key]['id'] = $value->id;
                    $new_data[$key]['icon'] = $value->icon;
                    $new_data[$key]['parent_title'] = ($value->depositHead->is_serial) ? $value->parent()->title : '';
                    $new_data[$key]['title'] = $value->title;
                    $new_data[$key]['is_serial'] = $value->depositHead->is_serial;
                    $new_data[$key]['created_at'] = date('d F Y', strtotime($value->created_at));
                }
                // dd($new_data);
                return $new_data;
            });
            // dd($getCache);
            return response()->json($getCache);
        } else if ($for == 'collection_last_day') {
            $getCache = Cache::remember('collection_last_day', 600, function () use ($publisher_id) {
                $date_before         = date('Y-m-d', strtotime('-10 days'));
                $date_now            = date('Y-m-d');
                $where_raw           = "DATE(collections.created_at) >= '$date_before' AND DATE(collections.created_at) <= '$date_now'";

                $getData =  DepositHead::selectRaw('
                    COUNT(collections.id) AS total, 
                    deposit_head.id as deposit_head_id,
                    deposit_head.shape,
                    deposit_head.is_serial
                ')
                    ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
                    ->where('collections.publisher_id', $publisher_id)
                    ->whereRaw($where_raw)
                    ->groupBy('deposit_head.id')
                    ->orderBy('deposit_head.id', 'desc')
                    ->get();

                $new_data = [];
                foreach ($getData as $value) {
                    $new_data[$value->shape] = $value->total;
                }

                $data['labels'] = array_keys($new_data);
                $datasets['label'] = 'Total';
                $datasets['data'] = array_values($new_data);
                $datasets['backgroundColor'] = $this->generateColor(array_values($new_data));
                $data['datasets'][] = $datasets;
                return $data;
            });
            return response()->json($getCache);
        } else if ($for == 'file_type') {
            $getCache = Cache::remember('file_type', 600, function () use ($publisher_id) {
                $file_type =  CollectionMedia::select('extension', DB::raw('count(*) as Total'))
                    ->rightJoin('collections', 'collections.id', 'collection_media.collection_id')
                    ->where('collections.publisher_id', $publisher_id)
                    ->groupBy('extension')
                    ->get()
                    ->pluck('Total', 'extension');
                $data['labels'] = ['PDF', 'WAV', 'MPEG'];
                $datasets['label'] = 'Total';
                $datasets['data'] = [
                    isset($file_type['pdf']) ? $file_type['pdf'] : 0,
                    isset($file_type['wav']) ? $file_type['wav'] : 0,
                    isset($file_type['mpeg']) ? $file_type['mpeg'] : 0,
                ];
                $datasets['backgroundColor'] = '#17A2B8';
                $datasets['hoverBackgroundColor'] = '#17A2B8';
                $datasets['borderColor'] = 'transparent';
                $data['datasets'][] = $datasets;
                return $data;
            });
            return response()->json($getCache);
        } else if ($for == 'activity') {
            $getCache = Cache::remember('activity', 600, function () use ($user) {
                $data = ActivityLog::where('causer_id', $user->id)->limit(5)->latest()->get();
                $new_data = [];
                foreach ($data as $key => $value) {
                    $new_data[$key] = $value;
                    $new_data[$key]['username'] = $value->user->username;
                }
                return $new_data;
            });
            return response()->json($getCache);
        }
    }

    public function generateColor($data)
    {
        $colors = [];
        for ($index = 1; $index <= count($data); $index++) {
            $colors[] = $this->selectColor($index);
        }

        return $colors;
    }

    public function selectColor($number)
    {
        $hue = $number * 137.508 + 60; // use golden angle approximation
        return "hsl($hue, 50%, 75%)";
    }
}
