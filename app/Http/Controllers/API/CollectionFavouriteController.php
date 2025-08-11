<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;
use App\Models\CollectionFavourite;

class CollectionFavouriteController extends Controller
{

    public function save(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'pemustaka_id'                          => 'required',
            'collection_id'                         => 'required',
            'tanggal_favorit'                           => 'required',
        ], [
            'pemustaka_id.required'                    => 'Id Pemustaka wajib di isi!',
            'collection_id.required'                   => 'Id Koleksi wajib di isi!',
            'tanggal_favorit.required'                 => 'Tanggal Favorit wajib di isi!',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
            return response()->json($response);
        }

        try {

            $favorite = CollectionFavourite::where('pemustaka_id', $request->pemustaka_id)
                ->where('collection_id', $request->collection_id)
                ->first();

            if (!$favorite) {
                CollectionFavourite::create([
                    'pemustaka_id'      => $request->pemustaka_id,
                    'collection_id'     => $request->collection_id,
                    'tanggal_favorit'    => date('Y-m-d H:i:s', strtotime($request->tanggal_favorit)),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'Failed Created Collection Favourite. Server Error',
                'err'       => $e->getMessage(),
                'status'    => 'Failed'
            ], 500);
        }

        return response()->json([
            'message'   => 'Success Created Collection Favourite.',
            'status'    => 'Success'
        ], 201);
    }

    public function get(Request $request)
    {


        $query = CollectionFavourite::select(
            'id',
            'pemustaka_id',
            'collection_id',
            'tanggal_favorit'
        );

        if ($request->pemustaka_id) {
            $query->where('pemustaka_id', $request->pemustaka_id);
        }

        $collectionFavourite = $query->get();

        return response()->json($collectionFavourite, 200);
    }
}
