<?php

namespace App\Http\Controllers\Admin;

use App\Models\Solr;
use App\Models\Province;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillIsbnController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Tagihan ISBN',
            'content' => 'admin.bill_isbn'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatableSummary(Request $request)
    {
        $where_like = [
            'kd_penerbit',
            'nama_penerbit',
            'provinsi',
            'percentage',
            'total_elek_diminta',
            'total_cetak_diminta',
            'total_elek_diterima',
            'total_cetak_diterima',
            'total_tagihan_elek',
            'total_tagihan_cetak',
            'total_all'
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
                'kd_penerbit'   => '"' . $search . '"'
            ]);
        }

        if ($request->province_id) {
            $province = Province::find($request->province_id);
            array_push($data, ['provinsi' => '"' . $province->name . '"']);
        }

        if ($request->publisher_id) {
            $specific = ['kd_penerbit' => $request->publisher_id];
        } else {
            $specific = [];
        }

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
            $summary    = Solr::summaryBillIsbn('isbn', $d['kd_penerbit'], $request);
            $total_bill = number_format($summary['total_all_bill']);
            $total_rest = number_format($summary['total_all_rest']);
            $total_all  = '<span style="font-size:12px;" class="font-weight-bold text-italic">' . $total_rest . '</span> / ' . $total_bill;

            $response['data'][] = [
                $nomor,
                '<span data-toggle="tooltip" title="' . $d['nama_penerbit'] . '">' . Str::limit($d['nama_penerbit'], 20) . '</span>',
                $d['provinsi'],
                $summary['percentage'] . '%',
                number_format($summary['request_elek']),
                number_format($summary['request_cetak']),
                number_format($summary['received_elek']),
                number_format($summary['received_cetak']),
                number_format($summary['total_bill_elek']),
                number_format($summary['total_bill_cetak']),
                $total_all
            ];

            $nomor++;
        }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }

    public function datatableDetail(Request $request)
    {
        $where_like = [
            'kd_penerbit_dtl',
            'nama_penerbit',
            'provinsi',
            'title',
            'jenis',
            'received_date',
            'code',
            'kepeng',
            'created_date'
        ];

        $data   = [];
        $offset = $request->start;
        $limit  = $request->length;
        $order  = $where_like[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        if ($request->param) {
            if ($request->param == 'annual') {
                $start  = $request->year_start . '-01-01T00:00:00Z';
                $finish = $request->year_end . '-12-31T23:59:59Z';
            } else if ($request->param == 'monthly') {
                $start  = $request->month_year_start . '-' . $request->month_start . '-01T00:00:00Z';
                $finish = date('Y-m-t', strtotime($request->month_year_end . '-' . $request->month_end)) . 'T23:59:59Z';
            } else if ($request->param == 'daily') {
                $start  = $request->day_start . 'T00:00:00Z';
                $finish = $request->day_end . 'T23:59:59Z';
            }

            if (isset($request->type_date)) {
                array_push($data, [$request->type_date => "[$start TO $finish]"]);
            } else {
                array_push($data, ['created_date' => "[$start TO $finish]"]);
            }
        }

        if ($request->province_id) {
            $province = Province::find($request->province_id);
            array_push($data, ['provinsi' => '"' . $province->name . '"']);
        }

        if ($request->publisher_id) {
            $specific = ['kd_penerbit' => $request->publisher_id];
        } else {
            $specific = [];
        }

        if ($request->type) {
            array_push($data, ['jenis' => $request->type == 1 ? 'elek' : 'cetak']);
        }
        if ($request->title) {
            array_push($data, ['title' => '"' . $request->title . '"']);
        }
        if ($request->code) {
            array_push($data, ['code' => '*' . str_replace('-', '', $request->code) . '*']);
        }
        if ($request->kepeng) {
            array_push($data, ['kepeng' => '*' . $request->kepeng . '*']);
        }

        if ($request->status) {
            if ($request->status == 1) {
                array_push($data, ['-received_date' => "[* TO *]"]);
            } else {
                array_push($data, ['received_date' => "[$start TO $finish]"]);
            }
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

            $response['data'][] = [
                $nomor,
                $d['nama_penerbit'],
                $d['provinsi'],
                $d['title'],
                ucwords($d['jenis']),
                $received_date,
                $code,
                $d['kepeng'],
                $created_date
            ];

            $nomor++;
        }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }
}
