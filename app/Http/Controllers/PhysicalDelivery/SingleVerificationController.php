<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\QueryAPI;

class SingleVerificationController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'physical-delivery.single-verification',
                'plugins' => [
                    'datatable',
                    'daterangepicker',
                    'select2',
                    'epubjs',
                    'videojs',
                    'pdfjs',
                    'howlerjs',
                ]
            ]
        ]);
    }

    public function search(Request $request)
    {
        Log::info('he');
        $keyword = trim((string) $request->get('keyword', ''));
        $mode = strtolower(trim((string) $request->get('mode', 'auto')));
        $limit = (int) $request->get('limit', 20);

        if ($limit < 1 || $limit > 100) {
            $limit = 20;
        }

        if ($keyword === '') {
            return response()->json([
                'code' => 200,
                'message' => 'Keyword kosong',
                'data' => [],
            ]);
        }

        $keywordSafe = $this->escapeSql($keyword);
        $keywordUpper = strtoupper($keywordSafe);

        if ($mode === 'auto') {
            $isbnOnly = preg_replace('/[^0-9Xx]/', '', $keyword);

            if (
                preg_match('/^[0-9Xx\-\s]+$/', $keyword) &&
                in_array(strlen($isbnOnly), [10, 13])
            ) {
                $mode = 'isbn';
            } else {
                $mode = 'title';
            }
        }
        $where = " l.status != 'DRAFT' ";
        if ($mode === 'isbn') {
            $where .= " AND
                REPLACE(REPLACE(UPPER(NVL(ld.ISBN, '')), '-', ''), ' ', '') 
                LIKE '%' || REPLACE(REPLACE(UPPER('{$keywordUpper}'), '-', ''), ' ', '') || '%'
            ";
        } else {
            $where .= " AND
                UPPER(NVL(ld.TITLE, '')) LIKE '%' || UPPER('{$keywordUpper}') || '%' ESCAPE '\'
            ";
        }
        $where .= " AND l.branch_id = '" . session('branch_id') . "'";

        $sql = "
            SELECT *
            FROM (
                SELECT
                    ld.letter_detail_id, ld.title, 
                    ld.copy, ld.quantity, ld.qty_accept, ld.qty_reject, ld.qty_hibah,
                    ld.isbn, ld.publisher, ld.publish_year, 
                    p.name as pub_name, l.status, l.type_of_delivery, 
                    jp.name as jasa_pengiriman_name,
                    ld.received_date,
                    l.accept_date,
                    b.name as DESTINATION_LIBRARY,
                CASE 
                    WHEN ld.received_date is null THEN 'verification'
                    ELSE 'received' 
                END AS status_code
                FROM LETTER_DETAIL ld
                JOIN PENERBIT p on p.id = ld.penerbit_id
                JOIN LETTER l on l.letter_id = ld.letter_id
                LEFT JOIN JASA_PENGIRIMAN jp on l.jasa_pengiriman_id = jp.id
                JOIN BRANCHS b on l.branch_id = b.id
                WHERE {$where}
                ORDER BY l.CREATE_DATE DESC
            )
            WHERE ROWNUM <= {$limit}
        ";
        //Log::info($sql);
        $data = QueryAPI::get($sql);
        return response()->json([
            'code' => 200,
            'message' => 'Berhasil',
            'mode' => $mode,
            'count' => count($data),
            'data' => $data,
        ]);
    }

    private function escapeSql(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace("'", "''", $value);
        $value = str_replace('%', '\%', $value);
        $value = str_replace('_', '\_', $value);

        return $value;
    }
}
