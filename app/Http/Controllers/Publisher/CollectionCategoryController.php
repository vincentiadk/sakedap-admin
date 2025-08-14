<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CollectionCategoryController extends Controller
{
	public function getCategoryByTye(Request $request, $type)
	{

		$cat = Category::select('id', 'name')
			->where('type', $type)
			->get();

		return response()->json($cat);
	}
}
