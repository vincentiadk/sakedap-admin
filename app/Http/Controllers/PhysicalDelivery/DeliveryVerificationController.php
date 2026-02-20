<?php

namespace App\Http\Controllers\PhysicalDelivery;

use Carbon\Carbon;
use App\Helpers\ISBN;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DeliveryVerificationController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'receivedBy' => QueryAPI::get("select distinct(received_by) from letter_detail where received_by is not null") ?? [],
                'content' => 'physical-delivery.delivery-verification',
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
            'l.letter_id',
            null,
            'p.name',
            'l.status',
            'l.sent_date',
            'l.check_date',
            'l.receipt_no',
            'jp.name',
            'b.name',
            null,
            null,
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "l.status in ('TERKIRIM', 'CEK FISIK')";

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $whereCondition[] = 'b.province_id = ' . session('province_id');
        }

        if ($request->received_by) {
            $whereCondition[] = "ld.received_by = '$request->received_by'";
        }

        if ($request->receipt_no) {
            $receiptNo = strtoupper($request->receipt_no);
            $whereCondition[] = "upper(l.receipt_no) like '%$receiptNo%'";
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "l.jasa_pengiriman_id = $request->delivery_service_id";
        }

        if ($request->status) {
            $whereCondition[] = "l.status = '$request->status'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "l.penerbit_id = $request->executor_id";
        }

        if ($request->branch_id) {
            $whereCondition[] = "l.branch_id = $request->branch_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(l.$request->date_type >= to_date('$startDate', 'YYYY-MM-DD') and l.$request->date_type < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($request->title) {
            $filterText = strtoupper($request->title);
            $whereCondition[] = "upper(ld.title) like '%$filterText%'";
        }

        if ($request->author) {
            $filterText = strtoupper($request->author);
            $whereCondition[] = "upper(ld.author) like '%$filterText%'";
        }

        if ($request->isbn) {
            $filterText = str_replace('-', '', $request->isbn);
            $whereCondition[] = "replace(ld.isbn, '-', '') = '$filterText'";
        }

        if ($request->publish_year) {
            $whereCondition[] = "ld.publish_year = '$request->publish_year'";
        }

        if ($request->edition_serial) {
            $filterText = strtoupper($request->edition_serial);
            $whereCondition[] = "upper(ld.edisi_serial) like '%$filterText%'";
        }

        if ($request->periodicals) {
            $filterText = strtoupper($request->periodicals);
            $whereCondition[] = "upper(ld.kala_terbit) like '%$filterText%'";
        }

        if ($request->physical_description) {
            $filterText = strtoupper($request->physical_description);
            $whereCondition[] = "upper(ld.deskripsifisik) like '%$filterText%'";
        }

        if ($request->sinopsis) {
            $filterText = strtoupper($request->sinopsis);
            $whereCondition[] = "upper(ld.sinopsis) like '%$filterText%'";
        }

        if ($request->media_type) {
            $filterText = strtoupper($request->media_type);
            $whereCondition[] = "upper(ld.jenis_media) like '%$filterText%'";
        }

        if ($request->binding) {
            $filterText = strtoupper($request->binding);
            $whereCondition[] = "upper(ld.nomorpanggiljilid) like '%$filterText%'";
        }

        if ($request->qrcbn) {
            $filterText = strtoupper($request->qrcbn);
            $whereCondition[] = "ld.qrcbn = '$filterText'";
        }

        if ($request->isbd) {
            $filterText = strtoupper($request->isbd);
            $whereCondition[] = "ld.isbd = '$filterText'";
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
                letter
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(distinct l.letter_id) as total
            from
                letter l
            left join
                penerbit p on p.id = l.penerbit_id
            left join
                jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
            left join
                branchs b on b.id = l.branch_id
            left join
                letter_detail ld on ld.letter_id = l.letter_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from
                (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select distinct
                                l.*,
                                b.name as name_branch,
                                jp.name as name_jasa_pengiriman,
                                p.name as name_penerbit,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK')
                                    then nvl(td.total_eks_delivery, 0)
                                    else 0
                                end as total_eks_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK')
                                    then nvl(td.total_title_delivery, 0)
                                    else 0
                                end as total_title_delivery
                            from
                                letter l
                            left join
                                penerbit p on p.id = l.penerbit_id
                            left join
                                jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
                            left join
                                branchs b on b.id = l.branch_id
                            left join
                                (
                                    select
                                        letter_id,
                                        sum(copy) as total_eks_delivery,
                                        sum(quantity) as total_title_delivery
                                    from
                                        letter_detail
                                    group by
                                        letter_id
                                ) td on td.letter_id = l.letter_id
                            left join
                                letter_detail ld on ld.letter_id = l.letter_id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $statusAccept = ['DITERIMA PENUH', 'DITERIMA PARSIAL'];
                $iconClass = in_array($val->STATUS, $statusAccept) ? 'ph-info' : 'ph-check';
                $buttonText = 'Detail';
                $detailUrl = url('physical-delivery/delivery-verification/detail/' . $val->LETTER_ID);

                $action = '
                    <button type="button" class="btn btn-primary btn-sm text-nowrap" data-url="' . $detailUrl . '" onclick="window.location.href = this.getAttribute(\'data-url\');"><i class="' . $iconClass . ' me-1"></i>' . $buttonText . '</button>
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm text-nowrap" onclick="destroyData(' . $val->LETTER_ID . ')"><i class="ph-trash me-1"></i>Hapus</a>
                ';

                $sentDateHTML = '
                    <div>' . Carbon::parse($val->SENT_DATE)->isoFormat('D MMM Y') . '</div>
                    <small class="text-muted">Jam : ' . Carbon::parse($val->SENT_DATE)->format('H:i') . ' WIB</small>
                ';

                $sentDateDB = $val->SENT_DATE;
                $checkDateDB = null;
                $sentDate = Carbon::parse($sentDateDB);

                if ($checkDateDB) {
                    $endDate = Carbon::parse($checkDateDB);
                } else {
                    $endDate = Carbon::now();
                }

                $seconds = round($sentDate->diffInSeconds($endDate));
                $minutes = round($sentDate->diffInMinutes($endDate));
                $hours = round($sentDate->diffInHours($endDate));
                $days = round($sentDate->diffInDays($endDate));

                if ($days > 0) {
                    $aging = '
                        <div>' . $days . ' Hari</div>
                    ';
                } else if ($hours > 0) {
                    $aging = '
                        <div>' . $hours . ' Jam</div>
                    ';
                } else if ($minutes > 0) {
                    $aging = '
                        <div>' . $minutes . ' Menit</div>
                    ';
                } else if ($seconds > 0) {
                    $aging = '
                        <div>' . $seconds . ' Detik</div>
                    ';
                } else {
                    $aging = 'Tidak Ada';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->STATUS,
                    $sentDateHTML,
                    $aging,
                    $val->RECEIPT_NO,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->NAME_BRANCH,
                    $val->TOTAL_TITLE_DELIVERY,
                    $val->TOTAL_EKS_DELIVERY,
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

    public function datatableCollection(Request $request)
    {
        $column = [
            'letter_detail_id',
            'checked',
            null,
            'title',
            'isbn',
            'nomorpanggiljilid',
            'edisi_serial',
            null,
            null,
            'qty_accept',
            'qty_reject',
            'remark',
            'isbn_status',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $search = strtoupper($request->search['value'] ?? '');
        $whereCondition = ["letter_id = " . intval($request->letter_id)];

        if ($search) {
            $terms = collect($column)
                ->filter()
                ->map(fn($c) => "upper($c) like '%$search%'")
                ->toArray();

            if ($terms) {
                $whereCondition[] = '(' . implode(' or ', $terms) . ')';
            }
        }

        $whereClause = 'where ' . implode(' and ', $whereCondition);
        $orderBy = '';

        if ($request->order) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDir = strtoupper($request->order[0]['dir']);

            if (isset($column[$orderColumnIndex]) && $column[$orderColumnIndex]) {
                $orderBy = "order by {$column[$orderColumnIndex]} $orderDir";
            }
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                letter_detail
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(letter_detail_id) as total
            from
                letter_detail
            $whereClause
        ", true)->TOTAL ?? 0;

        $endRow = $start + $length;
        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from (
                            select
                                *
                            from
                                letter_detail
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $endRow
                )
            where
                rnum > $start
        ");

        $data = [];

        if (!$queryData) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        }

        $isbnCodes = collect($queryData)
            ->pluck('ISBN')
            ->map(fn($isbn) => str_replace('-', '', $isbn))
            ->filter()
            ->unique()
            ->values();

        $isbnData = [];

        if ($isbnCodes->isNotEmpty()) {
            $isbnDataCollection = collect();

            foreach ($isbnCodes as $code) {
                $result = ISBN::get('search', ['code' => $code], true);

                if ($result) {
                    $isbnDataCollection->put($code, $result);
                }
            }

            $isbnData = $isbnDataCollection;
        }

        $letterDetails = [];

        if ($isbnCodes->isNotEmpty()) {
            $isbnList = $isbnCodes->map(fn($c) => "'$c'")->implode(',');

            $sqlLetterDetail = "
                select
                    isbn,
                    nvl(sum(qty_accept), 0) as total_letter_detail
                from
                    letter_detail
                where
                    isbn in ($isbnList)
                group by
                    isbn
            ";

            $letterDetailResult = QueryAPI::get($sqlLetterDetail);

            if ($letterDetailResult) {
                $letterDetails = collect($letterDetailResult)->keyBy('ISBN');
            }
        }

        $collections = [];

        if ($isbnCodes->isNotEmpty()) {
            $isbnList = $isbnCodes->map(fn($c) => "'$c'")->implode(',');

            $sqlCollection = "
                select
                    isbn,
                    count(id) as total
                from
                    collections
                where
                    isbn in ($isbnList) and
                    source_id = 6
                group by
                    isbn
            ";

            $collectionResult = QueryAPI::get($sqlCollection);

            if ($collectionResult) {
                $collections = collect($collectionResult)->keyBy('ISBN');
            }
        }

        $currentUsername = session('username');
        $isAdmin = Main::isSuperAdmin();
        $noFileCover = asset('assets/no-file.jpg');
        $problemRejectDefault = 'Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan.';
        $rowNumber = $start;

        foreach ($queryData as $val) {
            $randStr = Str::random(10);
            $code = str_replace('-', '', $val->ISBN);
            $totalSystem = 0;
            $totalSent = $val->COPY ?: 0;
            $fileCover = $noFileCover;

            $checked = $val->CHECKED;
            $receivedBy = $val->RECEIVED_BY;
            $isOpen = ($checked != 1 && empty($receivedBy));
            $isOwner = ($receivedBy == $currentUsername);
            $canEdit = $isAdmin || $isOpen || $isOwner;

            if ($code && isset($isbnData[$code])) {
                $getDataISBN = $isbnData[$code];

                if (!empty($getDataISBN->cover_file_name)) {
                    $fileCover = $getDataISBN->cover_file_name;
                }
            }

            if ($code) {
                $totalLetterDetail = $letterDetails[$code]->TOTAL_LETTER_DETAIL ?? 0;
                $totalCollection = $collections[$code]->TOTAL ?? 0;

                if ($totalLetterDetail > 0) {
                    $totalSystem = $totalLetterDetail;
                } elseif ($totalCollection > 0) {
                    $totalSystem = $totalCollection;
                }
            }

            $totalAccept = 0;
            $totalReject = $totalSent;

            if ($totalSystem == 0 || $totalSystem == 1) {
                if ($totalSent == 1) {
                    $totalAccept = 1;
                    $totalReject = 0;
                } else {
                    $totalAccept = $isAdmin ? 2 : 1;
                    $totalReject = $totalSent - $totalAccept;
                }
            }

            $maxAccept = ($totalSent >= 2) ? ($isAdmin ? 2 : 1) : 1;

            if ($canEdit) {
                $checkedAttr = $checked ? 'checked' : '';

                $checkedField = sprintf(
                    '<input type="checkbox" class="form-check-input checkbox-%s" onchange="checkedAction(%d, \'%s\')" %s>',
                    e($randStr),
                    intval($val->LETTER_DETAIL_ID),
                    e($randStr),
                    $checkedAttr
                );
            } else {
                $checkedField = e($receivedBy);
            }

            $coverHtml = sprintf(
                '<a href="%s" data-lightbox="cover-%s" data-title="%s"><img src="%s" class="img img-fluid img-thumbnail" style="max-width:70px;" alt="Cover"></a>',
                e($fileCover),
                e($code),
                e($val->TITLE),
                e($fileCover)
            );

            $nameAttr = $canEdit ? 'name="letter_detail_system[]"' : '';

            $totalSystemField = sprintf(
                '<input type="number" class="form-control form-control-plaintext total-system-%s" %s value="%d" readonly>',
                e($randStr),
                $nameAttr,
                intval($totalSystem)
            );

            $nameAttr = $canEdit ? 'name="letter_detail_quantity[]"' : '';

            $totalCopyField = sprintf(
                '<input type="number" class="form-control form-control-plaintext total-copy-%s" %s value="%d" readonly>',
                e($randStr),
                $nameAttr,
                intval($totalSent)
            );

            $optionAccept = '';

            for ($i = 0; $i <= $maxAccept; $i++) {
                $selected = ($totalAccept == $i) ? 'selected' : '';
                $optionAccept .= sprintf('<option value="%d" %s>%d</option>', $i, $selected, $i);
            }

            $nameAttr = $canEdit ? 'name="letter_detail_qty_accept[]"' : '';
            $disabledAttr = $canEdit ? '' : 'disabled';

            $totalAcceptField = sprintf(
                '<select class="form-select total-accept-%s" %s onchange="calculateQty(\'%s\', \'accept\')" %s>%s</select>',
                e($randStr),
                $nameAttr,
                e($randStr),
                $disabledAttr,
                $optionAccept
            );

            $optionReject = '';

            for ($i = 0; $i <= $totalSent; $i++) {
                $selected = ($totalReject == $i) ? 'selected' : '';
                $optionReject .= sprintf('<option value="%d" %s>%d</option>', $i, $selected, $i);
            }

            $nameAttr = $canEdit ? 'name="letter_detail_qty_reject[]"' : '';

            $totalRejectField = sprintf(
                '<select class="form-select total-reject-%s" %s onchange="calculateQty(\'%s\', \'reject\')" %s>%s</select>',
                e($randStr),
                $nameAttr,
                e($randStr),
                $disabledAttr,
                $optionReject
            );

            $remark = [];

            if (!empty($val->REMARK)) {
                $remark = array_filter(explode(';', $val->REMARK));

                if ($totalReject > 0 && !in_array($problemRejectDefault, $remark)) {
                    $remark[] = $problemRejectDefault;
                }
            } else if ($totalReject > 0) {
                $remark[] = $problemRejectDefault;
            }

            $optionRemark = '';

            foreach ($remark as $r) {
                $optionRemark .= sprintf(
                    '<option value="%s" selected>%s</option>',
                    e($r),
                    e($r)
                );
            }

            $nameAttr = $canEdit ? 'name="letter_detail_remark[][]"' : '';

            $remarkField = sprintf(
                '<select class="form-select remark-%s remark-field" %s multiple %s>%s</select>',
                e($randStr),
                $nameAttr,
                $disabledAttr,
                $optionRemark
            );

            $nameAttr = $canEdit ? 'name="letter_detail_note[]"' : '';

            $noteField = sprintf(
                '<input type="text" class="form-control note-%s" %s value="%s" placeholder="...................." %s>',
                e($randStr),
                $nameAttr,
                e($val->ISBN_STATUS ?? ''),
                $disabledAttr
            );

            $btnUpdate = '';

            if ($receivedBy == $currentUsername) {
                $btnUpdate = sprintf(
                    '<button type="button" class="btn btn-warning btn-sm" onclick="checkedAction(%d, \'%s\', %d)"><i class="ph-pen me-1"></i>Edit Data</button>',
                    intval($val->LETTER_DETAIL_ID),
                    e($randStr),
                    0,
                );
            }

            $data[] = [
                ++$rowNumber,
                $checkedField,
                $coverHtml,
                e($val->TITLE),
                e($val->ISBN),
                e($val->NOMORPANGGILJILID),
                e($val->EDISI_SERIAL),
                $totalSystemField,
                $totalCopyField,
                $totalAcceptField,
                $totalRejectField,
                $remarkField,
                $noteField,
                $btnUpdate,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function checkedAction(Request $request)
    {
        $letterDetailId = $request->letter_detail_id;
        $qtyAccept = $request->qty_accept;
        $qtyReject = $request->qty_reject;
        $ISBNStatus = $request->isbn_status;
        $remark = $request->remark;
        $username = session('username');
        $checked = $request->checked;
        $verif = $request->verif;

        $letterDetail = QueryAPI::get("select * from letter_detail where letter_detail_id = $letterDetailId", true);

        if (!$letterDetail) {
            return response()->json([
                'code' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        if (!empty($letterDetail->RECEIVED_BY)) {
            if ($letterDetail->RECEIVED_BY != $username) {
                return response()->json([
                    'code' => 403,
                    'message' => 'Data sudah diverifikasi oleh ' . $letterDetail->RECEIVED_BY
                ]);
            }
        }

        $payload = [
            'qty_accept' => $checked ? $qtyAccept : null,
            'qty_reject' => $checked ? $qtyReject : null,
            'remark' => $checked ? implode(';', $remark ?? []) : null,
            'isbn_status' => $checked ? $ISBNStatus : null,
            'received_by' => $checked ? $username : null,
            'received_date' => $checked ? date('Y-m-d H:i:s') : null,
            'checked' => $checked ? 1 : null,
        ];

        if (!$verif) {
            unset($payload['received_by']);
            unset($payload['received_date']);
            unset($payload['checked']);
        }

        QueryAPI::update('letter_detail', $letterDetailId, $payload, false);

        return response()->json([
            'code' => 200,
            'message' => !$verif ? 'Data verifikasi berhasil di ubah' : ($checked ? 'Data berhasil diverifikasi' : 'Data berhasil dibatalkan verifikasi')
        ]);
    }

    public function detail(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404, 'Invalid letter ID');
        }

        try {
            $letterSql = "
                select
                    l.*,
                    jp.name as name_jasa_pengiriman,
                    p.name as name_penerbit,
                    b.name as name_branch
                from
                    letter l
                left join
                    penerbit p on p.id = l.penerbit_id
                left join
                    jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
                left join
                    branchs b on b.id = l.branch_id
                where
                    l.letter_id = $id
            ";

            $letter = QueryAPI::get($letterSql, true);

            if (!$letter) {
                abort(404, 'Letter not found');
            }

            if ($request->ajax()) {
                try {
                    $status = $request->status;

                    $letterDetail = QueryAPI::get("
                        select
                            count(letter_id) as total_data,
                            count(case when received_by is not null then 1 end) as total_verification,
                            sum(nvl(qty_reject, 0)) as total_reject
                        from
                            letter_detail
                        where
                            letter_id = $id
                    ", true);

                    if ($letterDetail) {
                        if ($letterDetail->TOTAL_DATA == $letterDetail->TOTAL_VERIFICATION) {
                            if ($letterDetail->TOTAL_REJECT > 0) {
                                $status = 'DITERIMA PARSIAL';
                            } else {
                                $status = 'DITERIMA PENUH';
                            }
                        }
                    }

                    QueryAPI::update('letter', $id, [
                        'status' => $status,
                        'proses_by' => session('username'),
                        'is_verification_by' => session('username'),
                        'check_date' => ($status == 'CEK FISIK' && empty($letter->CHECK_DATE)) ? date('Y-m-d H:i:s') : $letter->CHECK_DATE,
                        'accept_date' => (in_array($status, ['DITERIMA PENUH', 'DITERIMA PARSIAL'])) ? date('Y-m-d H:i:s') : null,
                    ], false);

                    return response()->json([
                        'code' => 200,
                        'message' => 'Data telah disimpan'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error in AJAX request: ' . $e->getMessage(), [
                        'letter_id' => $id,
                        'param' => $request->input('param'),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return response()->json([
                        'code' => 500,
                        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                    ], 500);
                }
            }

            return view('layouts.index', [
                'data' => [
                    'letter' => $letter,
                    'content' => 'physical-delivery.delivery-verification-detail',
                    'plugins' => [
                        'select2',
                        'datatable',
                        'lightbox',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in detail function: ' . $e->getMessage(), [
                'letter_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ], 500);
            }

            abort(500, 'Terjadi kesalahan sistem');
        }
    }

    public function destroyData(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::delete('letter', $id);

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
