<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Helpers\ISBN;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $length = $start + intval($request->length ?? 10);

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

        if ($request->executor_name) {
            $whereCondition[] = "upper(p.name) LIKE '%" . strtoupper(trim($request->executor_name)) . "%'";
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
                    <a href="' . url('physical-delivery/delivery-verification/update-data/' . $val->LETTER_ID) . '" class="btn btn-warning btn-sm text-nowrap"><i class="ph-pen me-1"></i>Edit</a>
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm text-nowrap" onclick="destroyData(' . $val->LETTER_ID . ')"><i class="ph-trash me-1"></i>Hapus</a>
                ';

                $sentDateHTML = '
                    <div>' . Carbon::parse($val->SENT_DATE)->isoFormat('D MMM Y') . '</div>
                    <small class="text-muted">Jam : ' . Carbon::parse($val->SENT_DATE)->format('H:i') . ' WIB</small>
                ';

                $sentDateDB = $val->SENT_DATE;
                $checkDateDB = $val->CHECK_DATE ?? null;
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
        $search = strtoupper(str_replace('-', '', trim($request->search['value'])) ?? '');
        $whereCondition = ["letter_id = " . intval($request->letter_id)];

        if ($search) {
            $terms = collect($column)
                ->filter()
                ->map(fn($c) => "upper(replace($c, '-', '')) like '%$search%'")
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
                letter_detail where letter_id=$request->letter_id
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(letter_detail_id) as total
            from
                letter_detail
            $whereClause
        ", true)->TOTAL ?? 0;

        $endRow = $start + $length;
        $sql = "
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
        ";

        $queryData = QueryAPI::get($sql);
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

        $letterDetailIds = collect($queryData)
            ->pluck('LETTER_DETAIL_ID')
            ->filter()
            ->unique()
            ->values();

        $isbnData = [];
        $search = $isbnCodes->implode(',');
        $result = ISBN::get('search', [
            'code' => $search,
            'start' => 0,
            'length' => 5000
        ]);

        $isbnDataCollection = collect();

        if ($result && !empty($result->data)) {
            foreach ($result->data as $row) {
                $code = str_replace('-', '', $row->code ?? $row->isbn ?? '');
                if ($code) {
                    $isbnDataCollection->put($code, $row);
                }
            }
        }

        $isbnData = $isbnDataCollection;
        $letterDetails = [];

        if ($isbnCodes->isNotEmpty()) {
            $isbnList = $isbnCodes->map(fn($c) => "'$c'")->implode(',');
            $letterDetailIdsList = $letterDetailIds->map(fn($id) => "'$id'")->implode(',');

            $sqlLetterDetail = "
                select
                    isbn,
                    nvl(sum(qty_accept), 0) as total_letter_detail
                from
                    letter_detail
                where
                    isbn in ($isbnList) and
                    letter_detail_id not in ($letterDetailIdsList)
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
            $letterDetailIdsList = $letterDetailIds->map(fn($id) => "'$id'")->implode(',');

            $sqlCollection = "
                select
                    isbn,
                    count(id) as total
                from
                    collections
                where
                    isbn in ($isbnList) and
                    letter_detail_id not in ($letterDetailIdsList) and
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
        $isAdmin = Main::isSuperAdmin() || Main::isPerpusnas();
        $noFileCover = asset('assets/no-file.jpg');
        $problemRejectDefault = 'Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan.';
        $rowNumber = $start;

        foreach ($queryData as $val) {
            $randStr = Str::random(10);
            $code = str_replace('-', '', $val->ISBN);
            $totalSent = $val->COPY ?: 0;
            $totalSystem = 0;
            $fileCover = $noFileCover;

            $checked = $val->CHECKED;
            $receivedBy = $val->RECEIVED_BY;
            $isOpen = ($checked != 1 && empty($receivedBy));
            $isOwner = ($receivedBy == $currentUsername);
            $canEdit = $isAdmin || $isOpen || $isOwner;

            if ($code && isset($isbnData[$code])) {
                $getDataISBN = $isbnData[$code];
                $fileCover = Main::getCoverISBN($getDataISBN->cover_file_name ?? null);
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

            $dbAccept = isset($val->QTY_ACCEPT) ? intval($val->QTY_ACCEPT) : null;
            $dbReject = isset($val->QTY_REJECT) ? intval($val->QTY_REJECT) : null;

            if ($dbAccept !== null || $dbReject !== null) {
                $totalAccept = $dbAccept;
                $totalReject = $dbReject;
            } else {
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

        $letterDetail = QueryAPI::get(
            "
            select
                letter_detail.*,
                letter.branch_id as branch_id_letter
            from
                letter_detail
            left join
                letter on letter.letter_id = letter_detail.letter_id
            where
                letter_detail.letter_detail_id = $letterDetailId",
            true
        );

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

        if ($letterDetail->ISBN ?: null && $qtyAccept > 0) {
            QueryAPI::setReceiveDate([
                'LetterDetailId' => $letterDetailId,
                'NomorISBN' => $letterDetail->ISBN,
                'IsPerpusnas' => $letterDetail->BRANCH_ID_LETTER == 37 ? 1 : 0,
                'IsProvinsi' => $letterDetail->BRANCH_ID_LETTER != 37 ? 1 : 0,
                'TanggalTerima' => date('Y-m-d'),
            ]);
        }

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

    public function updateData(Request $request, $id)
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

            $letterDetailSql = "
                select
                    *
                from
                    letter_detail
                where
                    letter_id = $id
            ";


            $letter = QueryAPI::get($letterSql, true);
            $letterDetail = QueryAPI::get($letterDetailSql);
            $isbnMap = collect();

            $codes = collect($letterDetail ?? [])
                ->pluck('ISBN')
                ->map(fn($x) => str_replace('-', '', (string) $x))
                ->filter()
                ->unique()
                ->values();

            if ($codes->isNotEmpty()) {
                $result = ISBN::get('search', [
                    'code' => $codes->implode(','),
                    'start' => 0,
                    'length' => 5000
                ]);

                $isbnMap = collect($result->data ?? [])
                    ->keyBy(fn($row) => str_replace('-', '', (string) ($row->code ?? $row->isbn ?? '')));
            }
            if (!$letter) {
                abort(404, 'Letter not found');
            }

            if ($request->ajax()) {
                try {
                    $now = date('Y-m-d H:i:s');
                    $currentUser = session('username');
                    $cacheDuration = 60;
                    $totalPackage = 0;

                    if ($request->ci && is_array($request->ci)) {
                        foreach ($request->ci as $key => $ci) {
                            $code = $request->ci_code[$key] ?? null;
                            $editable = $request->ci_editable[$key] ?? false;

                            if ($request->ci_copy[$key]) {
                                if (!$code || $editable == false) continue;

                                $isbnCacheKey = "isbn:{$code}";
                                $isbn = Cache::remember($isbnCacheKey, $cacheDuration, function () use ($code) {
                                    return ISBN::get('search', ['code' => $code], true);
                                });

                                if (!$isbn) {
                                    continue;
                                }

                                $catalog = null;

                                $totalPackage++;

                                if ($isbn->is_kdt_valid == 1) {
                                    $catalogId = $isbn->catalog_id;
                                    $catalogCacheKey = "catalog:{$catalogId}";

                                    $catalog = Cache::remember($catalogCacheKey, $cacheDuration, function () use ($catalogId) {
                                        return QueryAPI::get("select * from catalogs where id = {$catalogId}", true);
                                    });
                                }

                                $letterDetailData = [
                                    'title' => $isbn->title,
                                    'copy' => $request->ci_copy[$key],
                                    'quantity' => 1,
                                    'letter_id' => $letter->LETTER_ID ?? null,
                                    'author' => $isbn->kepeng,
                                    'publisher' => $isbn->nama_penerbit,
                                    'isbn' => $code,
                                    'publish_year' => $isbn->tahun_terbit,
                                    'isbn_status' => 'berISBN',
                                    'penerbit_isbn_id' => $isbn->id ?? null,
                                    'catalog_id' => $isbn->is_kdt_valid == 1 ? $isbn->catalog_id : null,
                                    'province_id' => $isbn->province_id,
                                    'kab_id' => $catalog->CITY_ID ?? null,
                                    'deskripsifisik' => $catalog->DESCRIPTION ?? null,
                                    'sinopsis' => $isbn->sinopsis,
                                    'cleaning_note' => $isbn->keterangan,
                                    'jenis_media' => $isbn->jenis_media,
                                    'collection_type_id' => 2,
                                    'penerbit_terbitan_id' => $isbn->ptid,
                                    'penerbit_id' => $isbn->penerbit_id ?? null,
                                    'nomorpanggiljilid' => $isbn->keterangan,
                                ];
                                $detailId = $request->ci_detail_id[$key] ?? null;
                                if ($detailId) {
                                    QueryAPI::update('letter_detail', $detailId, $letterDetailData, false);
                                } else {
                                    $exists = QueryAPI::get("
                                        select * from letter_detail
                                        where letter_id = {$letter->LETTER_ID}
                                        and replace(trim(isbn), '-', '') = '{$code}'
                                    ", true);
                                    if (!$exists) {
                                        QueryAPI::create('letter_detail', $letterDetailData, false);
                                    }
                                }
                            }
                        }
                    }

                    if ($request->cni && is_array($request->cni)) {
                        foreach ($request->cni as $key => $cni) {
                            $editable = $request->cni_editable[$key] ?? false;

                            if ($editable == false) {
                                continue;
                            }

                            $totalPackage++;

                            $catalogId = $request->cni_catalog_id[$key] ?? null;
                            $catalog = null;

                            if ($catalogId) {
                                $catalogCacheKey = "catalog:detail:{$catalogId}";

                                $catalog = Cache::remember($catalogCacheKey, $cacheDuration, function () use ($catalogId) {
                                    $catalogQuery = "
                                        select
                                            catalogs.*,
                                            penerbit.name as name_penerbit,
                                            penerbit.alamat as alamat_penerbit,
                                            kabupaten.namakab as namakab,
                                            kabupaten.propinsiid as propinsiid
                                        from
                                            catalogs
                                        left join
                                            penerbit on penerbit.id = catalogs.penerbit_id
                                        left join
                                            kabupaten on kabupaten.id = penerbit.city_id
                                        where
                                            catalogs.id = $catalogId
                                    ";

                                    return QueryAPI::get($catalogQuery, true);
                                });
                            }

                            $title = $request->cni_title[$key] ?? null;
                            $author = $request->cni_author[$key] ?? null;
                            $year = $request->cni_year[$key] ?? null;
                            $physicalDescription = $request->cni_physical_description[$key] ?? null;
                            $executor = $letter->NAME_PENERBIT ?? null;
                            $binding = $request->cni_binding[$key] ?? null;
                            $qrcbn = $request->cni_qrcbn[$key] ?? null;
                            $isbd = $request->cni_isbd[$key] ?? null;
                            $media = strtoupper($request->cni_type[$key] ?? '');
                            $getCollectionMedia = null;

                            if ($media) {
                                $getCollectionMedia = QueryAPI::get("select * from collectionmedias where upper(name) = '$media'", true);
                            }

                            $price = 0;

                            if (isset($request->cni_price[$key])) {
                                $price = str_replace([',', '.'], '', $request->cni_price[$key]);
                            }

                            $letterDetailData = [
                                'title' => $title,
                                'copy' => $letter->BRANCH_ID == 37 ? 2 : 1,
                                'quantity' => 1,
                                'price' => $price,
                                'letter_id' => $letter->LETTER_ID ?? null,
                                'author' => $author,
                                'publisher' => $catalog->NAME_PENERBIT ?? $executor,
                                'publisher_address' => $catalog->ALAMAT_PENERBIT ?? null,
                                'publish_year' => $year,
                                'publisher_city' => $catalog->NAMAKAB ?? null,
                                'is_receivedate' => 1,
                                'catalog_id' => $catalogId,
                                'province_id' => $catalog->PROPINSIID ?? null,
                                'kab_id' => $catalog->CITY_ID ?? null,
                                'collection_type_id' => $catalog->COLLECTIONMEDIA_ID ?? ($getCollectionMedia->ID ?? null),
                                'deskripsifisik' => $physicalDescription,
                                'jenis_media' => $getCollectionMedia->NAME ?? null,
                                'penerbit_id' => $catalog->PENERBIT_ID ?? null,
                                'nomorpanggiljilid' => $binding,
                                'qrcbn' => $qrcbn,
                                'isbd' => $isbd,
                                'received_by' => $currentUser,
                                'received_date' => $now,
                                'checked' => 1,
                            ];
                            $detailId = $request->cni_detail_id[$key] ?? null;
                            if ($detailId) {
                                QueryAPI::update('letter_detail', $detailId, $letterDetailData, false);
                            } else {
                                QueryAPI::create('letter_detail', $letterDetailData, false);
                            }
                        }
                    }

                    if ($request->cp && is_array($request->cp)) {
                        foreach ($request->cp as $key => $cp) {
                            $editable = isset($request->cp_editable[$key]) ? $request->cp_editable[$key] : false;
                            $catalogId = isset($request->cp_catalog_id[$key]) ? $request->cp_catalog_id[$key] : null;
                            $manualTitle = isset($request->cp_manual_title[$key]) ? $request->cp_manual_title[$key] : null;

                            if (!isset($request->cp[$key]) || $editable == false) {
                                continue;
                            }

                            $catalog = null;

                            $totalPackage++;

                            if ($catalogId) {
                                $catalogCacheKey = "catalog:detail:{$catalogId}";

                                $catalog = Cache::remember($catalogCacheKey, $cacheDuration, function () use ($catalogId) {
                                    $catalogQuery = "
                                        select
                                            catalogs.*,
                                            penerbit.name as name_penerbit,
                                            penerbit.alamat as alamat_penerbit,
                                            kabupaten.namakab as namakab,
                                            kabupaten.propinsiid as propinsiid
                                        from
                                            catalogs
                                        left join
                                            penerbit on penerbit.id = catalogs.penerbit_id
                                        left join
                                            kabupaten on kabupaten.id = penerbit.city_id
                                        where
                                            catalogs.id = $catalogId
                                    ";

                                    return QueryAPI::get($catalogQuery, true);
                                });
                            }

                            $edition = $request->cp_edition[$key] ?? null;
                            $firstTTES = $request->cp_first_ttes[$key] ?? null;
                            $endTTES = $request->cp_end_ttes[$key] ?? null;

                            $letterDetailData = [
                                'title' => $catalog->TITLE ?? ($manualTitle ?? ''),
                                'copy' => $letter->BRANCH_ID == 37 ? 2 : 1,
                                'quantity' => 1,
                                'price' => $catalog->PRICE ?? null,
                                'letter_id' => $letter->LETTER_ID ?? null,
                                'author' => $catalog->AUTHOR ?? null,
                                'publisher' => $catalog->NAME_PENERBIT ?? ($letterExecutor->NAME ?? null),
                                'publisher_address' => $catalog->ALAMAT_PENERBIT ?? null,
                                'publish_year' => $catalog->PUBLISHYEAR ?? null,
                                'publisher_city' => $catalog->NAMAKAB ?? null,
                                'is_receivedate' => 1,
                                'edisi_serial' => $edition,
                                'ttes_awal' => $firstTTES,
                                'ttes_akhir' => $endTTES,
                                'catalog_id' => $catalogId,
                                'province_id' => $catalog->PROPINSIID ?? null,
                                'kab_id' => $catalog->CITY_ID ?? null,
                                'collection_type_id' => $catalog->COLLECTIONMEDIA_ID ?? null,
                                'penerbit_id' => $catalog->PENERBIT_ID ?? null,
                                'received_by' => $currentUser,
                                'received_date' => $now,
                                'checked' => 1,
                            ];

                            $detailId = $request->cp_detail_id[$key] ?? null;
                            if ($detailId) {
                                QueryAPI::update('letter_detail', $detailId, $letterDetailData, false);
                            } else {
                                QueryAPI::create('letter_detail', $letterDetailData, false);
                            }
                        }
                    }

                    QueryAPI::update('letter', $letter->LETTER_ID, [
                        'jumlah_paket' => $totalPackage
                    ], false);

                    return response()->json([
                        'code' => 200,
                        'message' => 'Data telah disimpan'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'code' => 500,
                        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                    ], 500);
                }
            }

            $collectionISBN = [];
            $collectionNonISBN = [];
            $collectionSerial = [];

            foreach ($letterDetail ?? [] as $ld) {
                $isbn = $ld->ISBN ?: null;
                $edisiSerial = $ld->EDISI_SERIAL ?: null;
                $TTESFirst = $ld->TTES_AWAL ?: null;
                $TTESLast = $ld->TTES_AKHIR ?: null;

                if (!empty($isbn)) {
                    $collectionISBN[] = $ld;

                    continue;
                }

                if (!empty($edisiSerial) || !empty($TTESFirst) || !empty($TTESLast)) {
                    $collectionSerial[] = $ld;

                    continue;
                }

                $collectionNonISBN[] = $ld;
            }

            $worksheetAnalog = Main::COLLECTION_ANALOG;
            $worksheetPrinted = Main::COLLECTION_PRINTED;

            $media = QueryAPI::get("
                select
                    collectionmedias.*
                from
                    collectionmedias
                join
                    worksheets on worksheets.id = collectionmedias.worksheet_id
                where
                    worksheets.category in ('$worksheetAnalog','$worksheetPrinted') and
                    collectionmedias.depositformat_code is not null
            ");

            return view('layouts.index', [
                'data' => [
                    'letter' => $letter,
                    'letterDetail' => $letterDetail,
                    'collectionISBN' => $collectionISBN,
                    'collectionSerial' => $collectionSerial,
                    'collectionNonISBN' => $collectionNonISBN,
                    'isbnMap'   => $isbnMap,
                    'acceptDefault' => 2,
                    'media' => $media ?? [],
                    'content' => 'physical-delivery.delivery-verification-update-data',
                    'plugins' => [
                        'select2',
                        'datatable',
                        'lightbox',
                        'daterangepicker',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
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

    public function destroyDataLD(Request $request, $id)
    {
        $id = $request->id;
        if (Str::contains($id, 'new')) {
            return;
        }

        try {
            QueryAPI::delete('letter_detail', $id);

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
