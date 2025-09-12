<?php

namespace App\Exports;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportManagerExport implements FromView, ShouldAutoSize
{
    use Exportable;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * worksheetCategoryAnalog
     *
     * @var mixed
     */
    private $worksheetCategoryAnalog;

    /**
     * worksheetCategoryDigital
     *
     * @var mixed
     */
    private $worksheetCategoryDigital;

    /**
     * worksheetCategoryPrinted
     *
     * @var mixed
     */
    private $worksheetCategoryPrinted;

    /**
     * __construct
     *
     * @param  mixed $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->worksheetCategoryAnalog = Main::COLLECTION_ANALOG;
        $this->worksheetCategoryDigital = Main::COLLECTION_DIGITAL;
        $this->worksheetCategoryPrinted = Main::COLLECTION_PRINTED;

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
        $whereCondition[] = 'penerbit.status = 3';
        $whereCondition[] = "penerbit.source_db = 'EDEPOSIT'";

        if ($request->type_id) {
            $whereCondition[] = "penerbit.jenis_id = $request->type_id";
        }

        if ($request->category_id) {
            $whereCondition[] = "penerbit.kategori_id = $request->category_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "penerbit.province_id = $request->province_id";
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        $result = QueryAPI::get("
            select
                penerbit.name,
                penerbit.alias,
                penerbit.alamat,
                penerbit.nosiup,
                penerbit.kontak1,
                penerbit.telp1,
                penerbit.fax1,
                penerbit.email1,
                penerbit.website,
                penerbit.kodepos,
                penerbit.rata_terbitan,
                penerbit.lembaga_penaung,
                parent.name as name_parent,
                penerbit_jenis.name AS name_penerbit_jenis,
                penerbit_kategori.name AS name_penerbit_kategori,
                propinsi.namapropinsi AS namapropinsi,
                kabupaten.namakab AS namakab,
                kecamatan.namakec AS namakec,
                kelurahan.namakel AS namakel,
                count(
                    case
                        when worksheets.category = '$this->worksheetCategoryAnalog'
                        then 1
                    end
                ) AS total_analog,
                count(
                    case
                        when worksheets.category = '$this->worksheetCategoryPrinted'
                        then 1
                    end
                ) AS total_printed,
                count(
                    case
                        when worksheets.category = '$this->worksheetCategoryDigital'
                        then 1
                    end
                ) AS total_digital
            from
                penerbit
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            left join
                kabupaten on kabupaten.id = penerbit.city_id
            left join
                kecamatan on kecamatan.id = penerbit.district_id
            left join
                kelurahan on kelurahan.id = penerbit.village_id
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                catalogs on catalogs.penerbit_id = penerbit.id
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            left join
                penerbit parent on penerbit.id = parent.parent_id
            $whereClause
            group by
                penerbit.name,
                penerbit.alias,
                penerbit.alamat,
                penerbit.nosiup,
                penerbit.kontak1,
                penerbit.telp1,
                penerbit.fax1,
                penerbit.email1,
                penerbit.website,
                penerbit.kodepos,
                penerbit.rata_terbitan,
                penerbit.lembaga_penaung,
                parent.name,
                penerbit_jenis.name,
                penerbit_kategori.name,
                propinsi.namapropinsi,
                kabupaten.namakab,
                kecamatan.namakec,
                kelurahan.namakel
        ");

        Log::error("
            select
                penerbit.name,
                penerbit.alias,
                penerbit.alamat,
                penerbit.nosiup,
                penerbit.kontak1,
                penerbit.telp1,
                penerbit.fax1,
                penerbit.email1,
                penerbit.website,
                penerbit.kodepos,
                penerbit.rata_terbitan,
                penerbit.lembaga_penaung,
                parent.name as name_parent,
                penerbit_jenis.name AS name_penerbit_jenis,
                penerbit_kategori.name AS name_penerbit_kategori,
                propinsi.namapropinsi AS namapropinsi,
                kabupaten.namakab AS namakab,
                kecamatan.namakec AS namakec,
                kelurahan.namakel AS namakel,
                count(
                    case
                        when worksheets.category = '$this->worksheetCategoryAnalog'
                        then 1
                    end
                ) AS total_analog,
                count(
                    case
                        when worksheets.category = '$this->worksheetCategoryPrinted'
                        then 1
                    end
                ) AS total_printed,
                count(
                    case
                        when worksheets.category = '$this->worksheetCategoryDigital'
                        then 1
                    end
                ) AS total_digital
            from
                penerbit
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            left join
                kabupaten on kabupaten.id = penerbit.city_id
            left join
                kecamatan on kecamatan.id = penerbit.district_id
            left join
                kelurahan on kelurahan.id = penerbit.village_id
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                catalogs on catalogs.penerbit_id = penerbit.id
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            left join
                penerbit parent on penerbit.id = parent.parent_id
            $whereClause
            group by
                penerbit.name,
                penerbit.alias,
                penerbit.alamat,
                penerbit.nosiup,
                penerbit.kontak1,
                penerbit.telp1,
                penerbit.fax1,
                penerbit.email1,
                penerbit.website,
                penerbit.kodepos,
                penerbit.rata_terbitan,
                penerbit.lembaga_penaung,
                parent.name,
                penerbit_jenis.name,
                penerbit_kategori.name,
                propinsi.namapropinsi,
                kabupaten.namakab,
                kecamatan.namakec,
                kelurahan.namakel
        ");

        return view('export.report-manager', [
            'request' => $request,
            'data' => $result
        ]);
    }
}
