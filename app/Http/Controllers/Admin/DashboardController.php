<?php

namespace App\Http\Controllers\Admin;

use App\Models\Province;
use App\Models\Collection;
use App\Models\ActivityLog;
use App\Models\DepositHead;
use Illuminate\Http\Request;
use App\Models\CollectionMedia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // dd($test);
        // dd($this->statistic('collection_grouped'));
        // $collection_type_status = [
        //     'book' => [
        //         'review'  => $this->statistic('collection_type_status', [1, 1]),
        //         'manage'  => $this->statistic('collection_type_status', [1, 2]),
        //         'problem' => $this->statistic('collection_type_status', [1, [3, 5]])
        //     ],
        //     'partitur' => [
        //         'review'  => $this->statistic('collection_type_status', [2, 1]),
        //         'manage'  => $this->statistic('collection_type_status', [2, 2]),
        //         'problem' => $this->statistic('collection_type_status', [2, [3, 5]])
        //     ],
        //     'map' => [
        //         'review'  => $this->statistic('collection_type_status', [3, 1]),
        //         'manage'  => $this->statistic('collection_type_status', [3, 2]),
        //         'problem' => $this->statistic('collection_type_status', [3, [3, 5]])
        //     ],
        //     'serial' => [
        //         'review'  => $this->statistic('collection_type_status', [4, 1]),
        //         'manage'  => $this->statistic('collection_type_status', [4, 2]),
        //         'problem' => $this->statistic('collection_type_status', [4, [3, 5]])
        //     ],
        //     'audio' => [
        //         'review'  => $this->statistic('collection_type_status', [5, 1]),
        //         'manage'  => $this->statistic('collection_type_status', [5, 2]),
        //         'problem' => $this->statistic('collection_type_status', [5, [3, 5]])
        //     ],
        //     'film' => [
        //         'review'  => $this->statistic('collection_type_status', [6, 1]),
        //         'manage'  => $this->statistic('collection_type_status', [6, 2]),
        //         'problem' => $this->statistic('collection_type_status', [6, [3, 5]])
        //     ],
        // ];
        // $total_collection = $this->statistic('total_collection');

        // $collection = $this->statistic('collection_status');

        // $collection_last_day = [
        //     'book'     => $this->statistic('collection_last_day', 1),
        //     'partitur' => $this->statistic('collection_last_day', 2),
        //     'peta'     => $this->statistic('collection_last_day', 3),
        //     'serial'   => $this->statistic('collection_last_day', 4),
        //     'audio'    => $this->statistic('collection_last_day', 5),
        //     'film'     => $this->statistic('collection_last_day', 6)
        // ];
        // $file_type =  CollectionMedia::select('extension', DB::raw('count(*) as Total'))
        //     ->groupBy('extension')
        //     ->get()
        //     ->pluck('Total', 'extension');

        $data = [
            'title'                  => 'Dashboard',
            // 'collection_monitoring'  => $this->statistic('collection_monitoring'),
            // 'collection_type_status' => $collection_type_status,
            // 'total_collection'       => $total_collection,
            // 'activity'               => ActivityLog::limit(5)->latest()->get(),
            // 'file_type'              => $file_type,
            // 'collection'             => $collection,
            // 'collection_last_day'    => $collection_last_day,
            // 'collection_grouped'     => $this->statistic('collection_grouped'),
            // 'collection_list'        => $this->statistic('collection_list'),
            // 'collection_location'    => $this->statistic('collection_location'),
            'content'                => 'admin.dashboard'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public static function statistic($for, $param = null)
    {
        if ($for == 'collection_monitoring') {
            if (!Cache::get('collection_monitoring')) {
                $data = Collection::where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })->where('parent_id', 0)->where('status', 1)->oldest('created_at')->limit(8)->get();
                Cache::put('collection_monitoring', $data, 600);
                return $data;
            } else {
                return Cache::get('collection_monitoring');
            }
        } else if ($for == 'total_collection') {
            if (!Cache::get('total_collection')) {
                $data =   Collection::select(
                    'type',
                    DB::raw('count(type) as Total,
                    CASE WHEN type=1 THEN "book"
                    WHEN type=2 THEN "partitur"
                    WHEN type=3 THEN "map"
                    WHEN type=4 THEN "serial"
                    WHEN type = 5 THEN "audio"
                    ELSE "film" END as typeB')
                )
                    ->where('parent_id', 0)
                    ->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('city', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        }
                    })->groupBy('type')
                    ->get()
                    ->pluck('Total', 'typeB');
                Cache::put('total_collection', $data, 600);
                return $data;
            } else {
                return Cache::get('total_collection');
            }
        } else if ($for == 'collection_status') {
            if (!Cache::get('collection_status')) {
                $data =  Collection::select(
                    'status',
                    DB::raw('count(status) as Total,
                CASE WHEN status = 1 THEN "collection_review"
                WHEN status = 2 THEN "collection_accept"
                WHEN status = 3 THEN "collection_problem"
                WHEN status = 4 THEN "collection_preprocess"
                ELSE "collection_rejects" END as statusB')
                )
                    ->where('parent_id', 0)
                    ->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('city', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        }
                    })
                    ->groupBy('status')
                    ->get()
                    ->pluck('Total', 'statusB');
                Cache::put('collection_status', $data, 600);
                return $data;
            } else {
                return Cache::get('collection_status');
            }
        } else if ($for == 'collection_last_day') {
            $cache_name = 'collection_last_day_' . $param . '_' . session('library_id');
            if (!Cache::get($cache_name)) {
                $data =  Collection::where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }

                    $date_before = date('Y-m-d', strtotime('-10 days'));
                    $date_now    = date('Y-m-d');
                    $where_raw   = "DATE(created_at) >= '$date_before' AND DATE(created_at) <= '$date_now'";
                    $query->whereRaw("DATE(created_at) >= '$date_before' AND DATE(created_at) <= '$date_now'");
                })
                    ->where('parent_id', 0)
                    ->where('type', $param)
                    ->count();
                Cache::put($cache_name, $data, 600);
                return $data;
            } else {
                return Cache::get($cache_name);
            }
        } else if ($for == 'collection_type_status') {
            if (is_array($param[1])) {
                $status = implode("_", $param[1]);
            } else {
                $status = $param[1];
            }
            $status .= '_' . $param[0];
            $cache_name = 'collection_type_status_' . $status . '_' . session('library_id');
            if (!Cache::get($cache_name)) {
                $data =  Collection::where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                    ->where(function ($query) use ($param) {
                        if (is_array($param[1])) {
                            $query->whereIn('status', $param[1])
                                ->whereNotNull('rejected_at')
                                ->whereNotNull('rejected_by');
                        } else {
                            if ($param[1] == 1) {
                                $query->where('status', $param[1]);
                            } else {
                                $query->where('status', $param[1])
                                    ->whereNotNull('received_at')
                                    ->whereNotNull('received_by');
                            }
                        }
                    })
                    ->where('parent_id', 0)
                    ->where('type', $param[0])
                    ->count();
                Cache::put($cache_name, $data, 600);
                return $data;
            } else {
                return Cache::get($cache_name);
            }
        } else if ($for == 'collection_grouped') {
            $getCache = Cache::remember('collection_grouped', 600, function () {
                $data =  DepositHead::selectRaw('count(collections.id) as total_collections, deposit_head.code, deposit_head.shape, deposit_head.category')
                    ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
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
                    $datasets['backgroundColor'] = self::generateColor(array_values($new_data['grouped'][$k]));
                    $labels[$k]['datasets'][] = $datasets;
                }

                $labels['total']['labels'] = array_keys($new_data['total']);
                $datasets['label'] = 'Koleksi';
                $datasets['data'] = array_values($new_data['total']);
                $datasets['backgroundColor'] = self::generateColor(array_values($new_data['total']));
                $labels['total']['datasets'][] = $datasets;
                return $labels;
            });
            return response()->json($getCache);
        } else if ($for == 'collection_list') {
            $getCache = Cache::remember('collection_list', 600, function () {
                $data =  DepositHead::selectRaw('count(collections.id) as total_collections, deposit_head.id, deposit_head.code, deposit_head.shape, deposit_head.is_serial')
                    ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
                    ->groupBy('deposit_head.code')
                    ->orderBy('total_collections', 'desc')
                    ->limit(5)
                    ->get();

                $new_data = [];
                foreach ($data as $index => $value) {
                    if ($value['is_serial']) {
                        $koleksi = Collection::where('deposit_head_id', $value['id'])->where('parent_id', '0')->count();
                        $edisi = Collection::where('deposit_head_id', $value['id'])->whereNotIn('parent_id', ['0'])->count();
                        $new_data[$index]['shape'] = $value['shape'];
                        $new_data[$index]['code'] = $value['code'];
                        $new_data[$index]['value'] = $koleksi . ' Judul, ' . $edisi . ' Edisi';
                        $new_data[$index]['total'] = $value['total_collections'];
                    } else {
                        $new_data[$index]['shape'] = $value['shape'];
                        $new_data[$index]['code'] = $value['code'];
                        $new_data[$index]['value'] = $value['total_collections'] . ' Judul';
                        $new_data[$index]['total'] = $value['total_collections'];
                    }
                }

                return $new_data;
            });
            return response()->json($getCache);
        } else if ($for == 'collection_location') {
            $getCache = Cache::remember('collection_location', 600, function () {
                $data =  Province::selectRaw('count(collection_copies.id) as total_exemplar, count(DISTINCT collections.id) as total_collection, provinces.id, provinces.name')
                    ->leftJoin('publishers', 'publishers.province_id', '=', 'provinces.id')
                    ->leftJoin('collections', 'publishers.id', '=', 'collections.publisher_id')
                    ->leftJoin('collection_copies', 'collections.id', '=', 'collection_copies.collection_id')
                    ->groupBy('provinces.id')
                    ->orderBy('total_collection', 'desc')
                    ->limit(10)
                    ->get();

                return $data;
            });
            return response()->json($getCache);
        } else if ($for == 'file_type') {
            $getCache = Cache::remember('file_type', 600, function () {
                $file_type =  CollectionMedia::select('extension', DB::raw('count(*) as Total'))
                    ->groupBy('extension')
                    ->get()
                    ->pluck('Total', 'extension');
                $data['labels'] = ['PDF', 'WAV', 'EPUB', 'JFIF', 'MP3', 'PNG', 'JPEG/JPG'];
                $datasets['label'] = 'Total';
                $datasets['data'] = [
                    $file_type['pdf'],
                    $file_type['wav'],
                    $file_type['epub'],
                    $file_type['jfif'],
                    $file_type['mp3'],
                    $file_type['png'],
                    $file_type['jpeg'] + $file_type['jpg']
                ];
                $datasets['backgroundColor'] = '#17A2B8';
                $datasets['hoverBackgroundColor'] = '#17A2B8';
                $datasets['borderColor'] = 'transparent';
                $data['datasets'][] = $datasets;

                return $data;
            });
            return response()->json($getCache);
        } else if ($for == 'activity') {
            $getCache = Cache::remember('activity', 600, function () {
                $data = ActivityLog::limit(5)->latest()->get();
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

    public static function generateColor($data)
    {
        $colors = [];
        for ($index = 1; $index <= count($data); $index++) {
            $colors[] = self::selectColor($index);
        }

        return $colors;
    }

    public static function selectColor($number)
    {
        $hue = $number * 137.508 + 60; // use golden angle approximation
        return "hsl($hue, 50%, 75%)";
    }
}
