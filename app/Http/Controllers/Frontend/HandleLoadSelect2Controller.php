<?php

namespace App\Http\Controllers\Frontend;

use App\Models\City;
use App\Models\Solr;
use App\Models\User;
use App\Models\Subject;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Library;
use App\Models\LibraryLocation;
use Illuminate\Http\Request;
use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Collection;
use App\Models\CollectionCopy;
use Illuminate\Support\Facades\DB;

class HandleLoadSelect2Controller extends Controller
{

    public function loadProvince(Request $request)
    {
        $search = $request->search;
        $data   = Province::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Semua Provinsi'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadCity(Request $request, $province_id = null)
    {
        $response = [];
        $search   = $request->search;

        if ($province_id) {
            $data = City::select('id', 'name')
                ->where('province_id', $province_id)
                ->where('name', 'like', "%{$search}%")
                ->get();
        } else {
            $data = City::select('id', 'name')
                ->where('name', 'like', "%{$search}%")
                ->get();
        }

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadDistrict(Request $request, $city_id = null)
    {
        $response = [];
        $search   = $request->search;

        if ($city_id) {
            $data = District::select('id', 'name')
                ->where('city_id', $city_id)
                ->where('name', 'like', "%{$search}%")
                ->get();
        } else {
            $data = District::select('id', 'name')
                ->where('name', 'like', "%{$search}%")
                ->get();
        }

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadVillage(Request $request, $district_id = null)
    {
        $response = [];
        $search   = $request->search;

        if ($district_id) {
            $data = Village::select('id', 'name')
                ->where('district_id', $district_id)
                ->where('name', 'like', "%{$search}%")
                ->get();
        } else {
            $data = Village::select('id', 'name')
                ->where('name', 'like', "%{$search}%")
                ->get();
        }

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
}
