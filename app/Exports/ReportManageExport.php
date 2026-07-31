<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportManageExport implements FromView, ShouldAutoSize
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
        // Basis e_collections — konsisten dengan datatable report/manage: semua yang
        // sudah diterima (status=2) ikut, baik yang sudah dikatalog maupun belum.
        $whereCondition[] = "e_collections.deleted_at is null";
        $whereCondition[] = "e_collections.status = '2'";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "upper(nvl(catalogs.title, e_collections.title)) like '%$title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "e_collections.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "e_collections.publication_year = $request->year";
        }

        if ($request->media_id) {
            $whereCondition[] = "e_collections.collection_media_id = $request->media_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e_collections.received_at >= to_date('$startDate', 'YYYY-MM-DD') and e_collections.received_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        $result = QueryAPI::get("
            select
                nvl(catalogs.title, e_collections.title) as title,
                nvl(catalogs.album, e_collections.album) as album,
                nvl(catalogs.series, e_collections.series) as series,
                nvl(catalogs.edition, e_collections.edition) as edition,
                catalogs.volume,
                catalogs.isbn,
                nvl(catalogs.publishyear, e_collections.publication_year) as publishyear,
                nvl(catalogs.preview, e_collections.preview) as preview,
                nvl(catalogs.createdate, e_collections.received_at) as createdate,
                penerbit.name as name_penerbit,
                propinsi.namapropinsi as namapropinsi,
                kabupaten.namakab as namakab,
                collectionmedias.name as name_media
            from
                e_collections
            left join
                catalogs on catalogs.edeposit_col_id = e_collections.id and nvl(catalogs.isdelete, 0) = 0
            left join
                penerbit on penerbit.id = e_collections.penerbit_id
            left join
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            left join
                propinsi on propinsi.id = kabupaten.propinsiid
            join
                collectionmedias on collectionmedias.id = e_collections.collection_media_id
            $whereClause
        ");

        return view('export.report-manage', [
            'request' => $request,
            'data' => $result ?? []
        ]);
    }
}
