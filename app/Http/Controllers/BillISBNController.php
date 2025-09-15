<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\ISBN;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillISBNController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'bill-isbn'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $draw = intval($request->draw ?? 0);
        $search = $request->search['value'];
        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);

        $filter = [
            'start' => $start,
            'length' => $length,
        ];

        if ($search) {
            $filter['search'] = $search;
        }

        if ($request->executor) {
            $filter['nama_penerbit'] = $request->executor;
        }

        if ($request->title) {
            $filter['title'] = $request->title;
        }

        if ($request->author) {
            $filter['kepeng'] = $request->author;
        }

        if ($request->year) {
            $filter['tahun_terbit'] = $request->year;
        }

        if ($request->city) {
            $filter['tempat_terbit'] = $request->city;
        }

        if ($request->code) {
            $filter['code'] = $request->code;
        }

        if ($request->subject) {
            $filter['subjek'] = $request->subject;
        }

        if ($request->media) {
            $filter['jenis_media'] = $request->media;
        }

        if ($request->province_id) {
            $filter['province_id'] = $request->province_id;
        }

        if ($request->received_date_kckr) {
            $explodeDate = explode(' - ', $request->received_date_kckr);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $filter['received_date_kckr_from'] = $startDate;
            $filter['received_date_kckr_to'] = $endDate;
        }

        if ($request->received_date_province) {
            $explodeDate = explode(' - ', $request->received_date_province);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $filter['received_date_prov_from'] = $startDate;
            $filter['received_date_prov_to'] = $endDate;
        }

        $data = [];
        $result = ISBN::get('search', $filter);
        $resultData = $result->data ?? [];

        if (count($resultData) > 0) {
            foreach ($resultData as $val) {
                if (isset($val->sinopsis)) {
                    $sinopsis = '
                        <button type="button" class="btn btn-light btn-sm" onclick="onPopover(this, ' . "'$val->sinopsis'" . ')">Lihat</button>
                    ';
                } else {
                    $sinopsis = '
                        <button type="button" class="btn btn-light btn-sm" disabled>Tidak Ada</button>
                    ';
                }

                if (isset($val->kepeng)) {
                    $author = '
                        <button type="button" class="btn btn-light btn-sm" onclick="onPopover(this, ' . "'$val->kepeng'" . ')">Lihat</button>
                    ';
                } else {
                    $author = '
                        <button type="button" class="btn btn-light btn-sm" disabled>Tidak Ada</button>
                    ';
                }

                $data[] = [
                    $start +  1,
                    isset($val->title) ? $val->title : '',
                    $author,
                    isset($val->nama_penerbit) ? $val->nama_penerbit : '',
                    isset($val->tahun_terbit) ? $val->tahun_terbit : '',
                    isset($val->tempat_terbit) ? $val->tempat_terbit : '',
                    isset($val->provinsi) ? $val->provinsi : '',
                    isset($val->isbn) ? $val->isbn : '',
                    isset($val->jenis_media) ? ucwords($val->jenis_media) : '',
                    isset($val->jenis_pustaka) ? ucwords($val->jenis_pustaka) : '',
                    isset($val->received_date_kckr) ? Carbon::parse($val->received_date_kckr)->format('d/m/Y') : '',
                    isset($val->received_date_prov) ? Carbon::parse($val->received_date_prov)->format('d/m/Y') : '',
                    $sinopsis,
                    isset($val->acceptdate) ? Carbon::parse($val->acceptdate)->format('d/m/Y') : '',
                    isset($val->createdate) ? Carbon::parse($val->createdate)->format('d/m/Y') : '',
                    isset($val->updatedate) ? Carbon::parse($val->updatedate)->format('d/m/Y') : '',
                ];

                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $result->recordsTotal ?? 0,
            'recordsFiltered' => $result->recordsFiltered ?? 0,
            'data' => $data,
        ]);
    }
}
