<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportPromotionExport implements FromView, ShouldAutoSize
{
    use Exportable;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * __construct
     *
     * @param  mixed $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;

        ini_set('memory_limit', '-1');
    }

    /**
     * view
     *
     * @return View
     */
    public function view(): View
    {
        $request = (object) $this->request;
        $whereClause = '';
        $whereCondition = [];

        if ($request->is_not_center_branch) {
            $whereCondition[] = 'penerbit.province_id = ' . $request->province_id;
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

        $result = QueryAPI::get("
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
        ");

        return view('export.report-promotion', [
            'request' => $request,
            'data' => $result ?? []
        ]);
    }
}
