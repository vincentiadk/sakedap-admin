<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class CollectionSubjectController extends Controller
{
	public function getSubject(Request $request)
	{

		$cat = Subject::select('id', 'name')
			->get();

		return response()->json($cat);
	}
}
