<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Solr;
use App\Models\User;
use App\Models\Author;
use App\Models\Library;
use App\Models\Subject;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\CollectionMedia;
use App\Models\LibraryLocation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HandleLoadSelect2Controller extends Controller
{
    public function loadPublisher(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Publisher::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Semua Publisher'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name,
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadLibrary(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Library::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->get();

        //testing performance using match against fulltext search
        // $data = DB::table('library_locations')
        //     ->selectRaw('id, name')
        //     ->whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", ['+' . $search . '*'])
        //     ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Pilih Perpustakaan'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name,
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadLibraryLocation(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = LibraryLocation::select('id', 'name')
            ->where('name', 'like', "%{$search}%")
            ->get();

        //testing performance using match against fulltext search
        // $data = DB::table('library_locations')
        //     ->selectRaw('id, name')
        //     ->whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", ['+' . $search . '*'])
        //     ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Pilih Lokasi Perpustakaan'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name,
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadAuthor(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Author::select('id', 'fullname')
            ->where('fullname', 'like', "%{$search}%")
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Pilih Author Collection'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->fullname,
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadCollectionEdition(Request $request, $collection_id)
    {
        $response = [];
        $search   = $request->search;
        $parent_id   = $collection_id;
        $data     = Collection::select('id', 'edition')
            ->where('parent_id', "$parent_id")
            ->where('edition', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Pilih Edisi'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->edition,
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadExtension(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = CollectionMedia::select('extension')
            ->where('extension', 'like', "%{$search}%")
            ->groupBy('extension')
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Semua Ekstensi'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->extension,
                'text' => $d->extension,
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadPublisherBill(Request $request)
    {
        $response = [];
        $search   = $request->search;

        if ($search) {
            $data = Solr::data('isbn', 'mst_penerbit', ['nama_penerbit' => '"' . $search . '"']);
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

    public function loadUser(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = User::select('id', 'username')
            ->where('username', 'like', "%{$search}%")
            ->where('userable_type', 'admins')
            ->get();

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->username
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadPublisherISRC(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = DB::connection('isrc')->table('producers')
            ->where('name', 'like', "%{$search}%")
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Semua'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->id,
                'text' => $d->name
            ];
        }

        return response()->json(['items' => $response]);
    }

    public function loadAuthorManage(Request $request)
    {
        $response = [];
        $search   = $request->search;
        $data     = Author::select('id', 'fullname')
            ->where('fullname', 'like', "%{$search}%")
            ->get();

        $response[] = [
            'id'   => '',
            'text' => 'Pilih Author Collection'
        ];

        foreach ($data as $d) {
            $response[] = [
                'id'   => $d->fullname,
                'text' => $d->fullname,
            ];
        }

        return response()->json(['items' => $response]);
    }
}
