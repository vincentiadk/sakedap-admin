<?php

namespace App\Http\Controllers\Publisher;

use App\Models\Solr;
use App\Models\User;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\PublisherWarning;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helper\GeneralHelper;
use App\Models\PublisherGroup;
use App\Models\PublisherAccess;
use App\Http\Controllers\Controller;

class BillIsbnController extends Controller
{

    public function index()
    {
        $user = User::find(session('id'));
        $groups = null;
        if ($user->publisher->getGroups()) {
            $groups = $user->publisher->getGroups()->groups;
        }
        if ($groups == null) {
            $publisher_groups = false;
            $publisher_user   = $user->publisher;
        } else {
            $publisher_groups = $groups->where('publisher_id', '!=', $user->publisher->id)->all();
            $publisher_user   = $groups->where('publisher_id', $user->publisher->id)->first();
            array_unshift($publisher_groups, $publisher_user);
        }

        $data = [
            'title'                     => 'Tagihan ISBN',
            'content'                   => 'publisher.bill_isbn',
            'code_system'               => $user->publisher->code_system,
            'publisher_groups'          => $publisher_groups
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatableSummary(Request $request)
    {
        $where_like = [
            'kd_penerbit',
            'nama_penerbit',
            'percentage',
            'total_elek_diminta',
            'total_cetak_diminta',
            'total_elek_diterima',
            'total_cetak_diterima',
            'total_tagihan_elek',
            'total_tagihan_cetak',
            'total_all'
        ];

        $data         = [];
        $offset       = $request->start;
        $limit        = $request->length;
        $order        = $where_like[$request->input('order.0.column')];
        $dir          = $request->input('order.0.dir');
        $search       = $request->input('search.value');
        $publisher_id = $request->input('publisher_id');

        if ($publisher_id) {
            $specific = ['kd_penerbit' => $publisher_id];
        } else {
            $user     = User::find(session('id'));
            $specific = ['kd_penerbit' => $user->publisher->code_system];
        }

        if ($search) {
            array_push($data, [
                'nama_penerbit' => '"' . $search . '"',
                'kd_penerbit'   => '"' . $search . '"'
            ]);
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
            'title',
            'jenis',
            'received_date',
            'code',
            'kepeng',
            'created_date'
        ];

        $data         = [];
        $offset       = $request->start;
        $limit        = $request->length;
        $order        = $where_like[$request->input('order.0.column')];
        $dir          = $request->input('order.0.dir');
        $search       = $request->input('search.value');
        $publisher_id = $request->input('publisher_id');

        if ($publisher_id) {
            $specific = ['kd_penerbit' => $publisher_id];
        } else {
            $user     = User::find(session('id'));
            $specific = ['kd_penerbit' => $user->publisher->code_system];
        }

        // dd($specific);

        if ($search) {
            array_push($data, [
                'nama_penerbit' => '"' . $search . '"',
                'title'         => '"' . $search . '"',
                'code'          => '"' . $search . '"',
                'kepeng'        => '"' . $search . '"'
            ]);
        }

        if ($request->type) {
            array_push($data, ['jenis' => $request->type == 1 ? 'elek' : 'cetak']);
        }

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

    public function totalBill(Request $request)
    {
        $user              = User::find(session('id'));
        $publisher_code    = $user->publisher->code_system;
        $summary_bill_isbn = Solr::summaryBillIsbn('isbn', $publisher_code, $request);

        $review = Collection::where('type', 1)
            ->where('code_type', 1)
            ->where('publisher_id', $user->publisher->id)
            ->where('status', 1)
            ->count();

        $problem = Collection::where('type', 1)
            ->where('code_type', 1)
            ->where('publisher_id', $user->publisher->id)
            ->where('status', 3)
            ->count();


        return response()->json([
            'total_elek'    => $summary_bill_isbn['total_bill_elek'] - $summary_bill_isbn['received_elek'],
            'total_cetak'   => $summary_bill_isbn['total_bill_cetak'] - $summary_bill_isbn['received_cetak'],
            'total_review'  => $review,
            'total_problem' => $problem
        ]);
    }

    public function warning(Request $request)
    {
        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;

        $warning = PublisherWarning::where('publisher_id', $publisher_id)->latest()->first();
        if (!empty($warning)) {
            return response()->json([
                'id' => $warning->id,
                'name' => $warning->publisher->name,
                'warning' => $warning->warning,
                'reason' => $warning->reason,
                'warning_date' => date('Y-m-d', strtotime($warning->warning_date)),
                'attachment_link' => asset(Storage::disk($warning->location->location)->url($warning->attachment)),
            ]);
        } else {
            return response()->json([]);
        }
    }

    public function locked(Request $request)
    {
        $user = User::find(session('id'));
        $publisher_id = $user->publisher->code_system;
        $locked = 0;
        return response()->json(['locked' => $locked]);
    }
}
