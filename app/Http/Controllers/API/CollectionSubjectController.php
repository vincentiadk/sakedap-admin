<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;
use App\Models\CollectionSubject;
use DB;

class CollectionSubjectController   extends Controller
{

    public function get(Request $request)
    {


        $subjects = CollectionSubject::select(DB::raw('COUNT(collection_subjects.subject_id) as total, subjects.name as name'))
            ->where('collections.parent_id', 0)
            ->join('collections', 'collections.id', 'collection_subjects.collection_id')
            ->join('subjects', 'subjects.id', 'collection_subjects.subject_id')
            ->groupBy('collection_subjects.subject_id')
            ->orderBy('total', 'DESC')
            ->take(10)
            ->get();

        return response()->json($subjects, 200);
    }
}
