<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;

class CollectionTypeController   extends Controller
{

    public function get(Request $request)
    {


        $type = [
            [
                'name'          => 'Buku',
                'total'         => Collection::where('type', 1)->where('parent_id', 0)->where('status', 2)->count()
            ],
            [
                'name'          => 'Partitur',
                'total'         => Collection::where('type', 2)->where('parent_id', 0)->where('status', 2)->count()
            ],
            [
                'name'          => 'Peta',
                'total'         => Collection::where('type', 3)->where('parent_id', 0)->where('status', 2)->count()
            ],
            [
                'name'          => 'Serial',
                'total'         => Collection::where('type', 4)->where('parent_id', 0)->where('status', 2)->count()
            ],
            [
                'name'          => 'Musik',
                'total'         => Collection::where('type', 5)->where('parent_id', 0)->where('status', 2)->count()
            ],
            [
                'name'          => 'Film',
                'total'         => Collection::where('type', 6)->where('parent_id', 0)->where('status', 2)->count()
            ]
        ];

        return response()->json($type, 200);
    }
}
