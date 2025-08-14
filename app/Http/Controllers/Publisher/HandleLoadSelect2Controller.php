<?php

namespace App\Http\Controllers\Publisher;

use App\Models\City;
use App\Models\Solr;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Village;
use App\Models\Category;
use App\Models\District;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Contributor;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HandleLoadSelect2Controller extends Controller
{

    public function loadPublisher(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Publisher::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadAuthor(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Author::select('id', 'fullname', 'title', 'year_of_birth', 'year_of_death')
            ->where('fullname', 'like', "%{$search}%")
            ->orderBy('fullname', 'asc')
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'        => $d->id,
                'text'      => $d->fullname,
                'title'     => $d->title,
                'yob'       => $d->year_of_birth,
                'yod'       => $d->year_of_death
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadPublisherBill(Request $request)
    {
        $response = [];
        $search   = $request->search;

        if ($search) {
            $data = Solr::data('isbn', 'mst_penerbit', ['nama_penerbit' => "$search"]);
        } else {
            $data = Solr::data('isbn', 'mst_penerbit');
        }

        $response[] = [
            'id'   => 'all',
            'text' => 'Semua Penerbit'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d['kd_penerbit'],
                'text' => $d['nama_penerbit']
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadProvince(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Province::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadCity(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $provinceId   = $request->nested_id;
        $data     = City::select('id', 'name')
            ->where('name', 'like', "%{$search}%");

        if ($provinceId) {
            $data->where('province_id', $provinceId);
        }

        $data = $data->get();


        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadDistrict(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $cityId   = $request->nested_id;
        $data     = District::select('id', 'name')
            ->where('name', 'like', "%{$search}%");

        if ($cityId) {
            $data->where('city_id', $cityId);
        }

        $data = $data->get();


        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadVillage(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $districtId   = $request->nested_id;
        $data     = Village::select('id', 'name')
            ->where('name', 'like', "%{$search}%");

        if ($districtId) {
            $data->where('district_id', $districtId);
        }

        $data = $data->get();


        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }



    public function loadCategory(Request $request, $type)
    {
        $response = [];
        $search   = $request->search;
        $data     = Category::select('id', 'name')
            ->where('type', $type)
            ->where('name', 'like', "%{$search}%")
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadSubject(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Subject::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->name,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadContributor(Request $request, $type)
    {
        $response = [];
        $search   = $request->search;
        $data     = Contributor::select('id', 'name')
            ->where('type', $type)
            ->where('name', 'like', "%{$search}%")
            ->where('show', 1)
            ->orderBy('name', 'asc')
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadCollection(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Collection::select('collections.*')
            ->join('deposit_head', 'deposit_head.id', 'collections.deposit_head_id')
            ->where('deposit_head.is_serial', "1")
            ->where('title', 'like', "%{$search}%")
            ->limit(5)
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Masukkan Judul'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->title,
                'collection' => $d,
            ];
        }

        return response()->json(['items' => $response]);
    }
}
