<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Collection;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\CollectionRequest;
use Storage;
use App\Helper\CustomTCPDF;

class CollectionSerialController extends Controller
{
    public function datatable(Request $request)
    {

        $whereLike = [
            'publisher_id',
            'title',
            'code',
            'created_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;

        $model = Collection::where('publisher_id', $publisher_id)
            ->where('parent_id', 0)
            ->where('type', 4);


        $totalData = $model->count();
        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
        } else {
            $totalFiltered = $model->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
                ->count();
            $queryData = $model->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $i => $val) {
                $response['data'][] = [
                    $i + 1,
                    $val->deposit,
                    $val->code,
                    $val->title,
                    "<button type='button' class='btn btn-sm btn-danger' name='select_serial' id='selectSerial_" . $i . "' onclick = 'selectSerial($val->id)'>Pilih</button>"
                ];
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function find($id)
    {

        $collection = Collection::where('id', $id)->with('collectionContributor.contributor', 'collectionContributor.author')->first();

        return response()->json($collection);
    }
}
