<?php

namespace App\Http\Controllers\Publisher\Api;

use Auth;
use App\Models\Solr;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillIsbnController extends Controller
{

    public function datatableDetail(Request $request)
    {
        $where_like = [
            'code',
            'title',
            'kepeng',
            'created_date',
            'received_date',
            'status',
            'jenis'
        ];

        $data         = [];
        $user         = Auth::user();
        $publisher_id = $user->publisher->code_system;

        $offset = $request->start;
        $limit  = $request->length;
        $order  = $where_like[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $jenis    = $request->input('jenis');
        $isbn     = $request->input('isbn');
        $status   = $request->input('status');
        $year     = $request->input('year');
        $month    = $request->input('month');
        $specific = ['kd_penerbit' => $publisher_id];

        if ($search) {
            array_push($data, [
                'code'   => '"' . $search . '"',
                'title'  => '"' . $search . '"',
                'kepeng' => '"' . $search . '"'
            ]);
        }

        if ($jenis) {
            array_push($data, ['jenis' => $jenis == 1 ? 'elek' : 'cetak']);
        }

        if ($status) {
            if ($status == 'review') {
                $status_arr = ['-received_date' => '[* TO *]'];
            } else if ($status == 'diterima') {
                $status_arr = ['received_date' => '[* TO *]'];
            } else if ($status == 'bermasalah') {
                $masalah    = Collection::select('code')->where('status', 3)->where('publisher_id', $user->publisher->id)->where('type', 1)->pluck('code');
                $status_arr = ['-received_date' => '[* TO *]', 'code' => str_replace('-', '', $masalah)];
            }

            array_push($data, $status_arr);
        }

        if ($year) {
            $monthly_start = $month ? $month : '01';
            $monthly_end   = $month ? $month : '12';
            $date_start    = $year . '-' . $monthly_start . '-01T00:00:00Z';
            $date_end      = date('Y-m-t', strtotime($year . '-' . $monthly_end)) . 'T23:59:59Z';

            array_push($data, ['created_date' => "[$date_start TO $date_end]"]);
        }

        $pagination = [
            'sort'   => $dir,
            'column' => $order,
            'offset' => $offset,
            'limit'  => $limit
        ];

        $datatable        = Solr::datatable('isbn', 'complete', Arr::collapse($data), $pagination, $specific);
        $response['data'] = [];
        $nomor            = $offset + 1;

        foreach ($datatable['result'] as $d) {
            $prefix_element    = $d['prefix_element'];
            $publisher_element = $d['publisher_element'];
            $item_element      = $d['item_element'];
            $check_digit       = $d['check_digit'];
            $code              = $prefix_element . '-' . $publisher_element . '-' . $item_element . '-' . $check_digit;

            if (isset($d['received_date'])) {
                $received_date = date('Y-m-d', strtotime($d['received_date']));
            } else {
                $received_date = '-';
            }

            if (isset($d['created_date'])) {
                $created_date = date('Y-m-d', strtotime($d['created_date']));
            } else {
                $created_date = '-';
            }

            if (Collection::whereRaw('REPLACE(code, "-", "") = ?', [$d['code']])->where('status', 3)->count() > 0) {
                $status = 'Masalah';
            } else if (Collection::whereRaw('REPLACE(code, "-", "") = ?', [$d['code']])->where('status', 2)->count() > 0) {
                $status = 'Diterima';
            } else {
                $status = 'Direview';
            }

            $response['data'][] = [
                $nomor,
                $code,
                $d['title'],
                $d['kepeng'],
                $created_date,
                $received_date,
                $status,
                ucwords($d['jenis'])
            ];

            $nomor++;
        }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }
}
