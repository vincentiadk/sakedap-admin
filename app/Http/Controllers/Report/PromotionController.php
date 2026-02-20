<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => !Main::isSuperAdmin(),
                'province_id' => session('province_id'),
                'promotion_id' => $request->action_by,
                'delivery_service_id' => $request->action_by,
                'executor_id' => $request->action_by,
                'date' => $request->date,
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-promotion', $payload)
                ->onQueue('report');

            return redirect('report/promotion')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'content' => 'report.promotion',
                'plugins' => [
                    'datatable',
                    'select2',
                    'daterangepicker',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_promo_transaksi.id',
            'e_promo.judul',
            'e_promo.kode_promo',
            'e_promo.saldo',
            'e_promo.diskon',
            'e_promo.jumlah_paket',
            'e_promo_transaksi.jumlah_potongan',
            'letter.letter_date',
            'letter.letter_number',
            'letter.sender',
            'penerbit.name',
            'jasa_pengiriman.name',
            'letter.receipt_no',
            'letter.biaya_kirim',
            'letter.berat',
            'letter.jumlah_paket',
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

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
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

        if ($request->promotion_id) {
            $whereCondition[] = "e_promo.id = $request->promotion_id";
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "letter.jasa_pengiriman_id = $request->delivery_service_id";
        }

        if ($request->executor_id) {
            $whereCondition[] = "letter.penerbit_id = $request->executor_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(letter.letter_date >= to_date('$startDate', 'YYYY-MM-DD') and letter.letter_date < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                e_promo_transaksi
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_promo_transaksi
            join
                e_promo on e_promo.id = e_promo_transaksi.promo_id
            join
                letter on letter.letter_id = e_promo_transaksi.letter_id
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
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
                                e_promo_transaksi.*,
                                e_promo.judul as judul_promo,
                                e_promo.saldo as saldo_promo,
                                e_promo.diskon as diskon_promo,
                                e_promo.jumlah_paket as jumlah_paket_promo,
                                e_promo.kode_promo as kode_promo_promo,
                                letter.letter_date as letter_date_letter,
                                letter.letter_number as letter_number_letter,
                                letter.sender as sender_letter,
                                letter.receipt_no as receipt_no_letter,
                                letter.biaya_kirim as biaya_kirim_letter,
                                letter.berat as berat_letter,
                                letter.jumlah_paket as jumlah_paket_letter,
                                penerbit.id as id_penerbit,
                                penerbit.name as name_penerbit,
                                jasa_pengiriman.name as name_jasa_pengiriman
                            from
                                e_promo_transaksi
                            join
                                e_promo on e_promo.id = e_promo_transaksi.promo_id
                            join
                                letter on letter.letter_id = e_promo_transaksi.letter_id
                            left join
                                penerbit on penerbit.id = letter.penerbit_id
                            left join
                                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rownum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    $val->JUDUL_PROMO,
                    $val->KODE_PROMO_PROMO,
                    'Rp ' . number_format($val->SALDO_PROMO ?: 0),
                    $val->DISKON_PROMO . ' %',
                    $val->JUMLAH_PAKET_PROMO,
                    'Rp ' . number_format($val->JUMLAH_POTONGAN ?: 0),
                    Carbon::parse($val->LETTER_DATE_LETTER)->isoFormat('dddd, D MMMM Y'),
                    $val->LETTER_NUMBER_LETTER,
                    $val->SENDER_LETTER,
                    $val->ID_PENERBIT . ' | ' . $val->NAME_PENERBIT,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->RECEIPT_NO_LETTER,
                    'Rp ' . number_format($val->BIAYA_KIRIM_LETTER ?: 0),
                    $val->BERAT_LETTER . ' gram',
                    $val->JUMLAH_PAKET_LETTER,
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
}
