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
        $status_isbn = QueryAPI::get("SELECT * FROM MASTER_STATUS_ISBN");
        return view('layouts.index', [
            'data' => [
                'content' => 'physical-delivery.single-verification',
                'plugins' => [
                    'select2',
                    'howlerjs',
                ], 
                'status_isbn' => $status_isbn
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
        $keywordUpper = trim(strtoupper($keywordSafe));
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

        $subWhere .= " and replace(trim(ld2.isbn), '-','') like '%' || replace(trim(ld.ISBN),'-','') || '%'";

        if ($mode === 'isbn') {
            $keywordUpper = str_replace('-','',$keywordUpper);
            $where .= " AND replace(ld.isbn, '-','') like '%{$keywordUpper}%'";
        } else {
            $where .= " AND upper(ld.title) like '%{$keywordUpper}%' ";
        }
        //$where .= " AND l.branch_id = {$branchId} ";
        $subWhereProv = $subWhere . " AND l2.branch_id != '37' ";
        $subWhere .= " AND l2.branch_id = '37' ";
        
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
                    ld.remark, ld.letter_id,
                    l.status, l.branch_id,
                    l.type_of_delivery,
                    ld.isbn_status,
                    jp.name AS jasa_pengiriman_name,
                    (
                        SELECT SUM(ld.copy)
                        FROM LETTER_DETAIL ld
                        JOIN LETTER l ON l.letter_id = ld.letter_id
                        WHERE {$where}
                    ) AS total_copy_all,
                    CASE
                        WHEN l.status IN ('DITERIMA PENUH', 'CEK FISIK', 'DITERIMA PARSIAL') THEN ld.received_date
                        WHEN l.status = 'DITERIMA' THEN l.accept_date
                        ELSE ld.received_date
                    END AS received_date,
                    CASE
                        WHEN l.status = 'DITERIMA' THEN u_l.fullname
                        WHEN l.status IN ('DITERIMA PENUH', 'CEK FISIK', 'DITERIMA PARSIAL') THEN u.fullname
                        ELSE u.fullname
                    END AS received_by_name,
                    l.accept_date,
                    b.name AS DESTINATION_LIBRARY,
                   (
                        SELECT SUM(ld2.copy)
                        FROM LETTER_DETAIL ld2
                        JOIN LETTER l2 ON l2.letter_id = ld2.letter_id
                        WHERE ld2.letter_detail_id != ld.letter_detail_id
                        AND {$subWhereProv}
                    ) AS total_copy_prov,
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
                        AND {$subWhereProv}
                    ) AS total_accept_prov,
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
                        AND replace(c.isbn, '-','') like '%' || replace(ld.ISBN, '-','') || '%'
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

    public function updateReceivedDate(Request $request)
    {
        $id = $request->letter_detail_id;
        try {
            if ($request->ISBN ?: null && (int) $request->detail_qty_accept > 0) {
                QueryAPI::setReceiveDate([
                        'LetterDetailId' => $id,
                        'NomorISBN' => $request->detail_isbn,
                        'IsPerpusnas' => $request->branch_id == 37 ? 1 : 0,
                        'IsProvinsi' => $request->branch_id != 37 ? 1 : 0,
                        'TanggalTerima' => $request->received_date,
                ]);
            }
            $params = [
                        'received_date' => $request->received_date,
                        'qty_accept' => $request->detail_qty_accept,
                        'qty_reject' => $request->detail_qty_reject,
                        'copy' => $request->detail_copy,
                        'remark' => $request->detail_reject_reason,
                        'checked' => 1
            ];
            if($request->received_by_name == "no_name"){
                $params = array_merge($params, [
                    'received_by' => session('username'),
                ]);
            }
            QueryAPI::update('letter_detail', $id, $params, false);
            $letterDetail = QueryAPI::get("
                select
                    sum(copy) as total_data,
                    sum(qty_accept) as total_accept,
                    sum(nvl(qty_reject, 0)) as total_reject
                from
                    letter_detail
                where
                    letter_id = '$request->letter_id'
                ", true);
            if ($letterDetail) {
                if ($letterDetail->TOTAL_DATA == $letterDetail->TOTAL_ACCEPT) {
                        $status = 'DITERIMA PENUH';
                    } else {
                        $status = 'DITERIMA PARSIAL';
                    }
                if(! $request->letter_status == 'DITERIMA') {
                    QueryAPI::update('letter', $request->letter_id, [
                            'status' => $status,
                            'accept_date' => $request->received_date,
                            'update_by' => session('username'),
                            'update_terminal' => $request->ip(),
                            'update_date' => date('Y-m-d')
                        ], false);
                } else {
                    QueryAPI::update('letter', $request->letter_id, [
                            'status' => $status,
                            'accept_date' => $request->received_date,
                            'update_by' => session('username'),
                            'update_terminal' => $request->ip(),
                            'update_date' => date('Y-m-d')
                        ], false);
                }
            }
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil menyimpan data penerimaan.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
