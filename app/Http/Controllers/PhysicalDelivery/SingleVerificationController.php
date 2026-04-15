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
        $branchId = (int) session('branch_id');

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
        $subWhere = " l2.status IN ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'CEK FISIK',  'DITERIMA') ";
        $subCollection = " c.source_id = 6 AND branch_id = '" . session('branch_id') . "'";

        if ($mode === 'isbn') {
            $isbnExprMain = "REPLACE(REPLACE(ld.ISBN), '-', ''), ' ', '')";
            $isbnExprSub  = "REPLACE(REPLACE(ld2.ISBN), '-', ''), ' ', '')";
            $keywordExpr  = "REPLACE(REPLACE('{$keywordUpper}', '-', ''), ' ', '')";

            $where .= " AND {$isbnExprMain} LIKE '%$keywordExpr%' ";
            $subWhere .= " AND {$isbnExprSub} LIKE '%$keywordExpr";
            $subCollection  .=  " AND {$isbnExprSub} LIKE '%$keywordExpr%' ";
        } else {
            $titleExprMain = "UPPER(ld.TITLE)";
            $titleExprSub  = "UPPER(ld2.TITLE)";
            $titleExprCol  = "UPPER(c.TITLE)";

            $where .= " AND {$titleExprMain} LIKE '%$keywordUpper%'";
            $subWhere .= " AND {$titleExprSub} LIKE '%$keywordUpper%'";
            $subCollection  .=  " AND {$titleExprCol} LIKE '%$keywordUpper%'";
        }

        $where .= " AND l.branch_id = {$branchId} ";
        $subWhere .= " AND l2.branch_id = {$branchId} ";

        $sql = "
            SELECT t.*,
             CASE
                WHEN t.received_date IS NULL THEN 'verification'
                ELSE 'received'
            END AS status_code
            FROM (
                SELECT
                    ld.letter_detail_id,
                    ld.title,
                    ld.copy,
                    ld.quantity,
                    ld.qty_accept,
                    ld.qty_reject,
                    ld.qty_hibah,
                    ld.isbn,
                    ld.publisher,
                    ld.publish_year,
                    p.name AS pub_name,
                    l.status,
                    l.type_of_delivery,
                    jp.name AS jasa_pengiriman_name,
                    CASE
                        WHEN l.status IN ('DITERIMA PENUH', 'CEK FISIK', 'DITERIMA PARSIAL') THEN ld.received_date
                        WHEN l.status = 'DITERIMA' THEN l.accept_date
                        ELSE NULL
                    END AS received_date,
                    CASE
                        WHEN l.status = 'DITERIMA' THEN u_l.fullname
                        WHEN l.status IN ('DITERIMA PENUH', 'CEK FISIK', 'DITERIMA PARSIAL') THEN u.fullname
                        ELSE NULL
                    END AS received_by_name,
                    l.accept_date,
                    b.name AS DESTINATION_LIBRARY,
                    (
                        SELECT SUM(ld2.copy)
                        FROM LETTER_DETAIL ld2
                        JOIN LETTER l2 ON l2.letter_id = ld2.letter_id
                        WHERE ld2.letter_detail_id != ld.letter_detail_id
                        AND {$subWhere}
                    ) AS total_copy_sistem,
                    (
                        SELECT SUM(ld2.qty_accept)
                        FROM LETTER_DETAIL ld2
                        JOIN LETTER l2 ON l2.letter_id = ld2.letter_id
                        WHERE ld2.letter_detail_id != ld.letter_detail_id
                        AND {$subWhere}
                    ) AS total_accept_sistem,
                    (
                        SELECT count(c.id)
                        FROM COLLECTIONS c
                        WHERE {$subCollection}
                    ) AS total_collection_sistem
                FROM LETTER_DETAIL ld
                JOIN PENERBIT p ON p.id = ld.penerbit_id
                JOIN LETTER l ON l.letter_id = ld.letter_id
                LEFT JOIN USERS u ON ld.received_by = u.username
                LEFT JOIN USERS u_l ON l.create_by = u_l.username
                LEFT JOIN JASA_PENGIRIMAN jp ON l.jasa_pengiriman_id = jp.id
                JOIN BRANCHS b ON l.branch_id = b.id
                WHERE {$where}
                ORDER BY l.CREATE_DATE DESC
            ) t
            WHERE ROWNUM <= {$limit}
        ";
       
        $data = QueryAPI::get($sql);
        //Log::info($sql);
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

    public function updateReceivedDate()
    {
        $id = $request->letter_detail_id;
        QueryAPI::update('letter_detail', $id, [
                'received_date' => $request->received_date,
                'received_by' => session('username'),
                'qty_accept' => $request->detail_qty_accept,
                'qty_reject' => $request->detail_qty_reject,
                'copy' => $request->detail->copy
            ], false);
        return true;
    }
}
