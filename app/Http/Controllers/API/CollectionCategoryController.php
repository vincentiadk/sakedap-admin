<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;
use App\Models\CollectionCategory;
use DB;

class CollectionCategoryController   extends Controller
{

    public function get(Request $request)
    {

        $categories = CollectionCategory::select(DB::raw('COUNT(collection_categories.category_id) as total, categories.name as name'))
            ->where('collections.parent_id', 0)
            ->join('collections', 'collections.id', 'collection_categories.collection_id')
            ->join('categories', 'categories.id', 'collection_categories.category_id')
            ->groupBy('collection_categories.category_id')
            ->orderBy('total', 'DESC')
            ->take(10)
            ->get();

        return response()->json($categories, 200);
    }
}
