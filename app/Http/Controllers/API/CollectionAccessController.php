<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;
use App\Models\CollectionAccess;

class CollectionAccessController extends Controller
{

	public function save(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'pemustaka_id'  => 'required',
			'collection_id' => 'required',
			'tanggal_akses' => 'required',
		], [
			'pemustaka_id.required'  => 'Id Pemustaka wajib di isi!',
			'collection_id.required' => 'Id Koleksi wajib di isi!',
			'tanggal_akses.required' => 'Tanggal Akses wajib di isi!',
		]);

		if ($validator->fails()) {
			$response = [
				'status' => 422,
				'error'  => $validator->errors()
			];
			return response()->json($response);
		}

		try {
			CollectionAccess::create([
				'pemustaka_id'      => $request->pemustaka_id,
				'collection_id'     => $request->collection_id,
				'tanggal_akses'    => date('Y-m-d H:i:s', strtotime($request->tanggal_akses)),
			]);
		} catch (\Exception $e) {
			return response()->json([
				'message'   => 'Failed Created Collection Access. Server Error',
				'err'       => $e->getMessage(),
				'status'    => 'Failed'
			], 500);
		}

		return response()->json([
			'message'   => 'Success Created Collection Access.',
			'status'    => 'Success'
		], 201);
	}

	public function get($id, Request $request)
	{


		$query = CollectionAccess::select(
			'id',
			'pemustaka_id',
			'collection_id',
			'tanggal_akses'
		)
			->where('pemustaka_id', $id);

		if ($request->collection_id) {
			$query->where('collection_id', $request->collection_id);
		}

		$collectionAccess = $query->get();

		return response()->json([
			'count'     => $collectionAccess->count(),
			'data'      => $collectionAccess
		], 200);
	}

	public function last($id, Request $request)
	{


		$query = CollectionAccess::select(
			'id',
			'pemustaka_id',
			'collection_id',
			'tanggal_akses'
		)
			->where('pemustaka_id', $id);

		$collectionAccess = $query->orderBy('tanggal_akses')->groupBy('collection_id')->limit(10)->get();

		return response()->json($collectionAccess, 200);
	}
}
