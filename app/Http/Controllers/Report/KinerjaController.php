<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KinerjaController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'report.kinerja',
                'plugins' => [
                    'datatable',
                    'daterangepicker',
                ],
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = ['id', 'action', 'tablename', 'actionby', 'actiondate', 'note'];

        $draw   = intval($request->draw   ?? 0);
        $start  = intval($request->start  ?? 0);
        $length = $start + intval($request->length ?? 10);

        $username = session('username');
        $whereCondition = ["actionby = '$username'", "tablename IN ('E_COLLECTIONS', 'CATALOGS', 'PENERBIT', 'e_collections', 'catalogs', 'penerbit')"];

        if ($request->date) {
            [$d0, $d1] = explode(' - ', $request->date);
            $startDate = Carbon::createFromFormat('Y/m/d', trim($d0))->format('Y-m-d');
            $endDate   = Carbon::createFromFormat('Y/m/d', trim($d1))->format('Y-m-d');
            $whereCondition[] = "(actiondate >= to_date('$startDate','YYYY-MM-DD') and actiondate < to_date('$endDate','YYYY-MM-DD') + 1)";
        }

        if ($request->action) {
            $act = str_replace("'", "''", $request->action);
            $whereCondition[] = "lower(action) = lower('$act')";
        }

        if ($request->table_name) {
            $tn = str_replace("'", "''", strtoupper($request->table_name));
            $whereCondition[] = "tablename = '$tn'";
        }

        $search = strtoupper(trim($request->search['value'] ?? ''));
        if ($search) {
            $s = str_replace("'", "''", $search);
            $terms = array_map(fn($c) => "upper($c) like '%$s%'", array_filter($column));
            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereCondition);

        $order = $request->order;
        $orderBy = 'ORDER BY actiondate DESC';
        if ($order && isset($column[$order[0]['column']])) {
            $dir = strtolower($order[0]['dir']) === 'asc' ? 'asc' : 'desc';
            $col = $column[$order[0]['column']];
            if ($col) $orderBy = "ORDER BY $col $dir";
        }

        $totalData = QueryAPI::get("
            SELECT COUNT(*) AS total FROM historydata WHERE actionby = '$username' AND tablename IN ('E_COLLECTIONS', 'CATALOGS', 'PENERBIT')
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total FROM historydata $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            SELECT * FROM (
                SELECT rownum AS rnum, data.* FROM (
                    SELECT id, action, tablename, actionby, actiondate, note
                    FROM historydata
                    $whereClause
                    $orderBy
                ) data WHERE rownum <= $length
            ) WHERE rnum > $start
        ") ?: [];

        $data = [];
        $no = $request->start ?? 0;
        foreach ($queryData as $val) {
            $no++;
            $data[] = [
                $no,
                $val->ACTION ?? '-',
                $val->TABLENAME ?? '-',
                $val->ACTIONBY ?? '-',
                $val->ACTIONDATE ? Carbon::parse($val->ACTIONDATE)->isoFormat('dddd, D MMMM Y HH:mm') : '-',
                $val->NOTE ?? '-',
            ];
        }

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    public function export(Request $request)
    {
        $username = session('username');
        $whereCondition = ["actionby = '$username'", "tablename IN ('E_COLLECTIONS', 'CATALOGS', 'PENERBIT')"];

        if ($request->date) {
            [$d0, $d1] = explode(' - ', $request->date);
            $startDate = Carbon::createFromFormat('Y/m/d', trim($d0))->format('Y-m-d');
            $endDate   = Carbon::createFromFormat('Y/m/d', trim($d1))->format('Y-m-d');
            $whereCondition[] = "(actiondate >= to_date('$startDate','YYYY-MM-DD') and actiondate < to_date('$endDate','YYYY-MM-DD') + 1)";
        }

        if ($request->action) {
            $act = str_replace("'", "''", $request->action);
            $whereCondition[] = "lower(action) = lower('$act')";
        }

        if ($request->table_name) {
            $tn = str_replace("'", "''", strtoupper($request->table_name));
            $whereCondition[] = "tablename = '$tn'";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereCondition);

        $rows = QueryAPI::get("
            SELECT id, action, tablename, actionby, actiondate, note
            FROM historydata
            $whereClause
            ORDER BY actiondate DESC
        ") ?: [];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kinerja');

        $headers = ['No', 'Aksi', 'Tabel', 'Username', 'Tanggal Aksi', 'Keterangan'];
        $sheet->fromArray($headers, null, 'A1');

        $styleHeader = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($styleHeader);

        $no = 1;
        foreach ($rows as $val) {
            $sheet->fromArray([
                $no++,
                $val->ACTION ?? '-',
                $val->TABLENAME ?? '-',
                $val->ACTIONBY ?? '-',
                $val->ACTIONDATE ? Carbon::parse($val->ACTIONDATE)->format('d/m/Y H:i') : '-',
                $val->NOTE ?? '-',
            ], null, "A$no");
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'laporan-kinerja-' . $username . '-' . date('Ymd-His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
