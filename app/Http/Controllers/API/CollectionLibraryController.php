<?php

namespace App\Http\Controllers\API;

use App\Helper\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Models\CollectionCategory;
use App\Models\CollectionContributor;
use Illuminate\Http\Request;
use App\Models\CollectionCopy;
use App\Models\CollectionMedia;
use App\Models\CollectionSubject;
use App\Models\CopyDelivery;
use Illuminate\Support\Facades\Storage;

class CollectionLibraryController extends Controller
{

    public function generateData($data, $param)
    {
        if ($param == 'contributors') {
            $data = $data->map(function ($item) {
                $response[GeneralHelper::toSnakeCase($item->contributor->name)] = $item->author->fullname . ',' . $item->author->title . ' [' . $item->author->year_of_birth . ' - ' . $item->author->year_of_death . ']';
                return $response;
            });
            return $data;
        } else if ($param == 'subjects') {
            $data = $data->map(function ($item) {
                return $item->subject->name;
            });
            return $data;
        } else if ($param == 'categories') {
            $data = $data->map(function ($item) {
                return $item->category->name;
            });
            return $data;
        }
    }

    public function date(Request $request)
    {
        $api_key = $request->header('X-API-KEY');
        $page = $request->has('page') && $request->query('page') != '' ? $request->query('page') : 1;
        $numPerPage = $request->has('num_per_page') && $request->query('num_per_page') != '' ? $request->query('num_per_page') : 10;
        $date_start = $request->has('date_start') && $request->query('date_start') != '' ? $request->query('date_start') : '';
        $date_end = $request->has('date_end') && $request->query('date_end') != '' ? $request->query('date_end') : '';

        if (!empty($date_start) && !empty($date_end)) {
            $date_start = date('Y-m-d', strtotime($date_start));
            $date_end = date('Y-m-d', strtotime($date_end));
            $collection = CollectionCopy::select(
                'collection_copies.id',
                'collection_copies.code as barcode',
                'collection_copies.condition',
                'collection_copies.availability',
                'copy_delivery_internals.delivery_internal_date',
                'copy_delivery_internals.accepted_date',
                'library_locations.name as location_name',
                'deposit_head.shape as deposit_shape',
                'deposit_head.code as deposit_code',
                'deposit_head.category as deposit_category',
                'deposit_head.is_serial',
                'collections.id as collection_id',
                'collections.title',
                'collections.edition',
                'collections.parent_id',
                'collections.publication_month',
                'collections.publication_year',
                'collections.description',
                'collections.start_publication_date',
                'collections.end_publication_date',
                'collections.mark_national',
                'collections.mark_province',
                'collections.code',
                'collections.currency',
                'collections.price',
                'collections.publisher_id',
                'publishers.name as publisher_name',
                'publishers.name_change as publisher_name_change',
                'publishers.organization_id as publisher_organization_id',
                'publishers.province_id as publisher_province_id',
                'provinces.name as publisher_province',
                'publishers.city_id as publisher_city_id',
                'cities.name as publisher_city',
                'publishers.district_id as publisher_district_id',
                'districts.name as publisher_district',
                'publishers.village_id as publisher_village_id',
                'villages.name as publisher_village',
                'publishers.photo as publisher_photo',
                'publishers.publisher_code as publisher_publisher_code',
                'publishers.contact as publisher_contact',
                'publishers.fax as publisher_fax',
                'publishers.phone as publisher_phone',
                'publishers.website as publisher_website',
                'publishers.address as publisher_address',
                'publishers.postal_code as publisher_postal_code',
                'publishers.type as publisher_type',
                'publishers.code_system as publisher_code_system',
                'publishers.system_type as publisher_system_type',
                'publishers.birth_certificate as publisher_birth_certificate',
                'publishers.statement_letter as publisher_statement_letter',
                'publishers.status as publisher_status',
                'publishers.birth_certificate_location as publisher_birth_certificate_location',
                'publishers.statement_letter_location as publisher_statement_letter_location',
                'parent.title as parent_title',
                'parent.description as parent_description',
                'parent.currency as parent_currency',
                'parent.price as parent_price',
                'parent.id as parent_id',
            )
                ->leftJoin('copy_delivery_internals', 'copy_delivery_internals.collection_copy_id', 'collection_copies.id')
                ->leftJoin('library_locations', 'collection_copies.lib_loc_id', 'library_locations.id')
                ->leftJoin('libraries', 'libraries.id', 'library_locations.library_id')
                ->leftJoin('collections', 'collections.id', 'collection_copies.collection_id')
                ->leftJoin('collections as parent', 'parent.id', 'collections.parent_id')
                ->leftJoin('deposit_head', 'collections.deposit_head_id', 'deposit_head.id')
                ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
                ->leftJoin('provinces', 'provinces.id', 'publishers.province_id')
                ->leftJoin('cities', 'cities.id', 'publishers.city_id')
                ->leftJoin('districts', 'districts.id', 'publishers.district_id')
                ->leftJoin('villages', 'villages.id', 'publishers.village_id')
                ->whereBetween('copy_delivery_internals.delivery_internal_date', [$date_start, $date_end]);

            if (isset($api_key)) {
                $collection->where('libraries.api_key', $api_key);
            }

            $total = $collection->count();

            $data = $collection
                ->limit($numPerPage)
                ->skip(($page - 1) * $numPerPage)
                ->groupBy('collection_copies.id')
                ->get();

            if ($total > 0) {
                $data = $data->map(function ($item) {
                    $response = $item;
                    $response['availability'] = $item->availability_text();
                    $response['condition'] = $item->condition_text();
                    $cover = CollectionMedia::where('collection_id', $item->collection_id)->where('type', 1)->first();
                    if ($cover && Storage::disk($cover->location->location)->exists($cover->link)) {
                        $url_cover = url('/collection/cover') . '/' . $cover->id;
                    } else {
                        $url_cover = '';
                    }
                    $response['cover'] = $url_cover;
                    $param_id = ($item->is_serial) ? $item->parent_id : $item->collection_id;
                    $response['contributors'] = $this->generateData(CollectionContributor::where('collection_id', $param_id)->get(), 'contributors');
                    $response['subjects'] = $this->generateData(CollectionSubject::where('collection_id', $param_id)->get(), 'subjects');
                    $response['categories'] = $this->generateData(CollectionCategory::where('collection_id', $param_id)->get(), 'categories');
                    return $response;
                });

                $response = [
                    'status'        => 'Success',
                    'data'          => $data->toArray(),
                    'page'          => $page,
                    'total'         => $total,
                    'num_per_page'   => $numPerPage
                ];

                return response()->json($response, 200);
            } else {
                return response()->json([
                    'message'       => 'Data Kosong',
                    'status'        => 'Failed'
                ], 401);
            }
        } else {
            return response()->json([
                'message'       => 'Mohon Pastikan Parameter "date_start" && "date_send" Terisi',
                'status'        => 'Failed'
            ], 401);
        }
    }

    public function barcode(Request $request)
    {
        $api_key = $request->header('X-API-KEY');
        $page = $request->has('page') && $request->query('page') != '' ? $request->query('page') : 1;
        $numPerPage = $request->has('num_per_page') && $request->query('num_per_page') != '' ? $request->query('num_per_page') : 10;
        $barcode = $request->has('barcode') && $request->query('barcode') != '' ? $request->query('barcode') : '';

        if (!empty($barcode)) {
            $barcode = explode(',', $barcode);
            if (sizeof($barcode) > 0) {
                $collection = CollectionCopy::select(
                    'collection_copies.id',
                    'collection_copies.code as barcode',
                    'collection_copies.condition',
                    'collection_copies.availability',
                    'library_locations.name as location_name',
                    'copy_delivery_internals.delivery_internal_date',
                    'copy_delivery_internals.accepted_date',
                    'deposit_head.shape as deposit_shape',
                    'deposit_head.code as deposit_code',
                    'deposit_head.category as deposit_category',
                    'deposit_head.is_serial',
                    'collections.id as collection_id',
                    'collections.title',
                    'collections.edition',
                    'collections.parent_id',
                    'collections.publication_month',
                    'collections.publication_year',
                    'collections.start_publication_date',
                    'collections.end_publication_date',
                    'collections.description',
                    'collections.mark_national',
                    'collections.mark_province',
                    'collections.code',
                    'collections.currency',
                    'collections.price',
                    'collections.publisher_id',
                    'publishers.name as publisher_name',
                    'publishers.name_change as publisher_name_change',
                    'publishers.organization_id as publisher_organization_id',
                    'publishers.province_id as publisher_province_id',
                    'provinces.name as publisher_province',
                    'publishers.city_id as publisher_city_id',
                    'cities.name as publisher_city',
                    'publishers.district_id as publisher_district_id',
                    'districts.name as publisher_district',
                    'publishers.village_id as publisher_village_id',
                    'villages.name as publisher_village',
                    'publishers.photo as publisher_photo',
                    'publishers.publisher_code as publisher_publisher_code',
                    'publishers.contact as publisher_contact',
                    'publishers.fax as publisher_fax',
                    'publishers.phone as publisher_phone',
                    'publishers.website as publisher_website',
                    'publishers.address as publisher_address',
                    'publishers.postal_code as publisher_postal_code',
                    'publishers.type as publisher_type',
                    'publishers.code_system as publisher_code_system',
                    'publishers.system_type as publisher_system_type',
                    'publishers.birth_certificate as publisher_birth_certificate',
                    'publishers.statement_letter as publisher_statement_letter',
                    'publishers.status as publisher_status',
                    'publishers.birth_certificate_location as publisher_birth_certificate_location',
                    'publishers.statement_letter_location as publisher_statement_letter_location',
                    'parent.title as parent_title',
                    'parent.description as parent_description',
                    'parent.id as parent_id',
                    'parent.currency as parent_currency',
                    'parent.price as parent_price',
                )
                    ->leftJoin('copy_delivery_internals', 'copy_delivery_internals.collection_copy_id', 'collection_copies.id')
                    ->leftJoin('library_locations', 'collection_copies.lib_loc_id', 'library_locations.id')
                    ->leftJoin('libraries', 'libraries.id', 'library_locations.library_id')
                    ->leftJoin('collections', 'collections.id', 'collection_copies.collection_id')
                    ->leftJoin('collections as parent', 'parent.id', 'collections.parent_id')
                    ->leftJoin('deposit_head', 'collections.deposit_head_id', 'deposit_head.id')
                    ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
                    ->leftJoin('provinces', 'provinces.id', 'publishers.province_id')
                    ->leftJoin('cities', 'cities.id', 'publishers.city_id')
                    ->leftJoin('districts', 'districts.id', 'publishers.district_id')
                    ->leftJoin('villages', 'villages.id', 'publishers.village_id')
                    ->whereIn('collection_copies.code', $barcode);

                if (isset($api_key)) {
                    $collection->where('libraries.api_key', $api_key);
                }

                $total = $collection->count();

                $data = $collection
                    ->limit($numPerPage)
                    ->skip(($page - 1) * $numPerPage)
                    ->groupBy('collection_copies.id')
                    ->get();

                if ($total > 0) {
                    $data = $data->map(function ($item) {
                        $response = $item;
                        $response['availability'] = $item->availability_text();
                        $response['condition'] = $item->condition_text();
                        $cover = CollectionMedia::where('collection_id', $item->collection_id)->where('type', 1)->first();
                        if ($cover && Storage::disk($cover->location->location)->exists($cover->link)) {
                            $url_cover = url('/collection/cover') . '/' . $cover->id;
                        } else {
                            $url_cover = '';
                        }
                        $response['cover'] = $url_cover;
                        $param_id = ($item->is_serial) ? $item->parent_id : $item->collection_id;
                        $response['contributors'] = $this->generateData(CollectionContributor::where('collection_id', $param_id)->get(), 'contributors');
                        $response['subjects'] = $this->generateData(CollectionSubject::where('collection_id', $param_id)->get(), 'subjects');
                        $response['categories'] = $this->generateData(CollectionCategory::where('collection_id', $param_id)->get(), 'categories');
                        return $response;
                    });

                    $response = [
                        'status'        => 'Success',
                        'data'          => $data->toArray(),
                        'page'          => $page,
                        'total'         => $total,
                        'num_per_page'  => $numPerPage
                    ];

                    return response()->json($response, 200);
                } else {
                    return response()->json(
                        [
                            'message'       => 'Data Kosong',
                            'status'        => 'Failed'
                        ],
                        401
                    );
                }
            } else {
                return response()->json([
                    'message'       => 'Mohon Pastikan Kode Barcode Terisi',
                    'status'        => 'Failed'
                ], 401);
            }
        } else {
            return response()->json([
                'message'       => 'Mohon Pastikan Parameter "barcode" Terisi',
                'status'        => 'Failed'
            ], 401);
        }
    }

    public function accept(Request $request)
    {
        if ($request->has('accepted_date') && $request->has('barcode')) {
            $accepted_date = date('Y-m-d', strtotime($request->accepted_date));
            $barcode = $request->barcode;
            $updateDelivery = CopyDelivery::whereHas('copy', function ($query) use ($barcode) {
                $query->where('code', $barcode);
            })
                ->update([
                    'accepted_date' => $accepted_date
                ]);

            if ($updateDelivery) {
                //set availability to "diterima di pengolahan"
                $collection_copy = CollectionCopy::where('code', $barcode)->first();
                $update = $collection_copy->update(['availability' => '9']);
                if ($update) {
                    $response = [
                        'status'        => 'Success',
                        'message'       => 'Success Update Accepted Date'
                    ];
                    return response()->json($response, 200);
                } else {
                    $response = [
                        'status'        => 'Failed',
                        'message'       => 'Gagal Update Status Availability Collection Copy'
                    ];
                    return response()->json($response, 200);
                }
            } else {
                return response()->json([
                    'message'       => 'Update Accepted Date Gagal, Mohon pastikan barcode valid dan eksemplar koleksi sudah dikirimkan',
                    'status'        => 'Failed'
                ], 401);
            }
        } else {
            return response()->json([
                'message'       => 'Mohon Pastikan parameter "barcode" dan parameter "accepted_date" terisi',
                'status'        => 'Failed'
            ], 401);
        }
    }
}
