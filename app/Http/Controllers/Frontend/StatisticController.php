<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\DepositHead;
use App\Models\Province;
use App\Models\Solr;
use Illuminate\Http\Request;
use App\Models\Tutorial;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $year_start = $request->input('year_start');
        $year_end = $request->input('year_end');
        $province_id = $request->input('province_id');

        $data =  DepositHead::where(function ($query) use ($year_start, $year_end, $province_id) {
            if (!empty($year_start) && !empty($year_end)) {
                $query->whereBetween('collections.created_at', [date('Y-m-d', strtotime($year_start . '-01-01')), date('Y-m-d', strtotime($year_end . '-12-31'))]);
            }
            if (!empty($province_id) && $province_id != 'null') {
                $query->where('publishers.province_id', $province_id);
            }
        })
            ->selectRaw('count(DISTINCT collections.id) as total_collections, deposit_head.code, deposit_head.shape, deposit_head.category')
            ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
            ->leftJoin('publishers', 'publishers.id', '=', 'collections.publisher_id')
            ->groupBy('deposit_head.category', 'deposit_head.code')
            ->orderBy('total_collections', 'desc')
            ->get();

        $depositHead = DepositHead::all()->toArray();

        $collection_location = Province::where(function ($query) use ($province_id) {
            if (!empty($province_id) && $province_id != 'null') {
                $query->where('publishers.province_id', $province_id);
            }
        })
            ->selectRaw('count(publishers.id) as total_publisher, provinces.id, provinces.name')
            ->leftJoin('publishers', 'publishers.province_id', '=', 'provinces.id')
            ->groupBy('provinces.id')
            ->orderBy('total_publisher', 'desc')
            ->limit(10)
            ->get();

        $dataSolr   = [];
        if (!empty($request->province_id) && $request->province_id != 'null') {
            $province = Province::find($request->province_id);
            array_push($dataSolr, ['provinsi' => '"' . $province->name . '"']);
        }

        // $collection_location = Solr::dataFacet('isbn', 'mst_penerbit', Arr::collapse($dataSolr), [], false, ['key' => 'provinsi_facet', 'value' => 'total_province']);

        // dd($collection_location);

        $new_data = [];
        foreach ($data as $value) {
            $new_data['grouped'][$value['category']][$value['code']] = $value['total_collections'];
            if (isset($new_data['total'][$value['category']])) {
                $new_data['total'][$value['category']] += $value['total_collections'];
            } else {
                $new_data['total'][$value['category']] = $value['total_collections'];
            }
        }

        // dd($new_data);

        foreach ($depositHead as $value) {
            if (!isset($new_data['grouped'][$value['category']][$value['code']])) {
                $new_data['grouped'][$value['category']][$value['code']] = 0;
            }

            if (!isset($new_data['total'][$value['category']])) {
                $new_data['total'][$value['category']] = 0;
            }
        }

        $province = Province::find($province_id);

        $data = [
            'title'                     => 'Statistic Edeposit - National Library of Indonesia',
            'chart'                     =>  $new_data,
            'collection_location'       =>  $collection_location,
            'collection_location'       =>  $collection_location,
            'year_start'                =>  $year_start,
            'year_end'                  =>  $year_end,
            'province_id'               =>  $province_id,
            'province'                  =>  isset($province->name) ? $province->name : '',
            'content'                   => 'frontend.statistic'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }

    public function detail($slug)
    {
        $tutorial = Tutorial::where('slug', $slug)->firstOrFail();

        $otherTutorial = Tutorial::where('publish', 1)->where('id', '<>', $tutorial->id)->limit(10)->orderBy('created_at', 'desc')->get();

        $data = [
            'title'                => $tutorial->title,
            'tutorial'                 => $tutorial,
            'othertutorial'          => $otherTutorial,
            'content'              => 'frontend.tutorial_detail'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }

    public function publisherISBNDatatable(Request $request)
    {
        $where_like = [
            'nama_penerbit',
            'percentage',
        ];

        $data   = [];
        $offset = $request->start;
        $limit  = $request->length;
        $order  = $where_like[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        if ($search) {
            array_push($data, [
                'nama_penerbit' => '"' . $search . '"',
            ]);
        }

        if (!empty($request->province_id) && $request->province_id != 'null') {
            $province = Province::find($request->province_id);
            array_push($data, ['provinsi' => '"' . $province->name . '"']);
        }

        $specific = [];

        $pagination = [
            'sort'   => $dir,
            'column' => $order,
            'offset' => $offset,
            'limit'  => $limit
        ];

        $datatable        = Solr::datatable('isbn', 'mst_penerbit', Arr::collapse($data), $pagination, $specific);
        $response['data'] = [];
        $nomor            = $offset + 1;

        foreach ($datatable['result'] as $d) {
            $summary    = Solr::summaryBillIsbnFrontend('isbn', $d['kd_penerbit'], $request);
            $percentage = $summary['percentage'] == "-" ? $summary['percentage'] : $summary['percentage'] . '%';
            // if ($summary['percentage'] != '-' && $summary['percentage'] != '0') {
            $response['data'][] = [
                $nomor,
                '<span data-toggle="tooltip" title="' . $d['nama_penerbit'] . '">' . Str::limit($d['nama_penerbit'], 20) . '</span>',
                $percentage,
            ];
            // }
            $nomor++;
        }

        // if(sizeof($response['data']) < $limit){
        //     array_merge($response['data'], )
        // }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }
}
