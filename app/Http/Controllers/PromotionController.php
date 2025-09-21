<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'promotion'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'id',
            null,
            'judul',
            'kode_promo',
            'tanggal_mulai',
            'tanggal_selesai',
            'saldo',
            'diskon',
            'jumlah_paket',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "$c like '%$search%'";
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
                e_promo
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_promo
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
                                *
                            from
                                e_promo
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

                $startDate = '
                    <div>' . Carbon::parse($val->TANGGAL_MULAI)->isoFormat('D MMM Y') . '</div>
                    ' . Carbon::parse($val->TANGGAL_MULAI)->format('H:i') . ' WIB
                ';

                $endDate = '
                    <div>' . Carbon::parse($val->TANGGAL_SELESAI)->isoFormat('D MMM Y') . '</div>
                    ' . Carbon::parse($val->TANGGAL_SELESAI)->format('H:i') . ' WIB
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->JUDUL,
                    $val->KODE_PROMO,
                    $startDate,
                    $endDate,
                    'Rp ' . number_format($val->SALDO),
                    $val->DISKON . ' %',
                    $val->JUMLAH_PAKET,
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
            'start_date' => 'required',
            'end_date' => 'required',
            'balance' => 'required|min:1',
            'discount' => 'required|min:1|max:100',
            'package' => 'required|min:1',
            'code' => 'required',
        ], [
            'title.required' => 'Judul tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai tidak boleh kosong',
            'end_date.required' => 'Tanggal berakhir tidak boleh kosong',
            'balance.required' => 'Saldo tidak boleh kosong',
            'balance.min' => 'Saldo minimal 1 rupiah',
            'discount.required' => 'Diskon tidak boleh kosong',
            'discount.min' => 'Diskon minimal 1%',
            'discount.max' => 'Diskon maksimal 100%',
            'package.required' => 'Jumlah paket tidak boleh kosong',
            'package.min' => 'Jumlah paket minimal 1',
            'code.required' => 'Kode tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_promo', [
                    'judul' => $request->title,
                    'tanggal_mulai' => Carbon::parse($request->start_date)->format('Y-m-d H:i:s'),
                    'tanggal_selesai' => Carbon::parse($request->end_date)->format('Y-m-d H:i:s'),
                    'saldo' => str_replace(',', '', $request->balance),
                    'diskon' => $request->discount,
                    'jumlah_paket' => $request->package,
                    'kode_promo' => $request->code,
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);

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
                e_promo
            where
                id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'balance' => 'required|min:1',
            'discount' => 'required|min:1|max:100',
            'package' => 'required|min:1',
            'code' => 'required',
        ], [
            'title.required' => 'Judul tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai tidak boleh kosong',
            'end_date.required' => 'Tanggal berakhir tidak boleh kosong',
            'balance.required' => 'Saldo tidak boleh kosong',
            'balance.min' => 'Saldo minimal 1 rupiah',
            'discount.required' => 'Diskon tidak boleh kosong',
            'discount.min' => 'Diskon minimal 1%',
            'discount.max' => 'Diskon maksimal 100%',
            'package.required' => 'Jumlah paket tidak boleh kosong',
            'package.min' => 'Jumlah paket minimal 1',
            'code.required' => 'Kode tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_promo', $id, [
                    'judul' => $request->title,
                    'tanggal_mulai' => Carbon::parse($request->start_date)->format('Y-m-d H:i:s'),
                    'tanggal_selesai' => Carbon::parse($request->end_date)->format('Y-m-d H:i:s'),
                    'saldo' => str_replace(',', '', $request->balance),
                    'diskon' => $request->discount,
                    'jumlah_paket' => $request->package,
                    'kode_promo' => $request->code,
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);

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
            QueryAPI::delete('e_promo', $id);

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
