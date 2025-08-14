<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Collection;
use App\Models\CollectionMedia;
use App\Models\User;
use App\Helper\GeneralHelper;

class SerialBulkEditionController extends Controller
{

    public function datatable(Request $request)
    {

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;

        $totalData = Collection::where('type', 4)
            ->where('publisher_id', $publisher_id)
            ->whereIn('status', [1, 2])
            ->where('parent_id', 0)
            ->count();
        if (empty($search)) {
            $queryData = Collection::where('type', 4)
                ->where('publisher_id', $publisher_id)
                ->whereIn('status', [1, 2])
                ->where('parent_id', 0)
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where('type', 4)
                ->where('publisher_id', $publisher_id)
                ->whereIn('status', [1, 2])
                ->where('parent_id', 0)
                ->count();
        } else {
            $queryData = Collection::where('type', 4)
                ->where('publisher_id', $publisher_id)
                ->whereIn('status', [1, 2])
                ->where('parent_id', 0)
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where('type', $type)
                ->where('publisher_id', $publisher_id)
                ->whereIn('status', [1, 2])
                ->where('parent_id', 0)
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $response['data'][] = [
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<a href="' . url('publisher/collection/monitoring/detail/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    date('d-m-Y', strtotime($val->created_at)),
                    "<button type='button' class='btn btn-sm btn-danger' name='select_serial'>Pilih</button>",
                    $val
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
}
