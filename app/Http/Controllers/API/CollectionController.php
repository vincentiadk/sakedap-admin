<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionMedia;
use App\Models\CollectionFavourite;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\Subject;
use App\Helper\GeneralHelper;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{

    public function getAll(Request $request)
    {

        $page = isset($request->page) ? $request->page : 1;
        $numOfPage = isset($request->num_of_page) ? $request->num_of_page : 10;

        $collection = Collection::select(
            'collections.type',
            'collections.title',
            'collections.code',
            'collections.deposit',
            'collections.id',
            'collections.access',
            'collections.preview',
            'collections.publication_year',
            'publishers.name as publsiher_name',
            DB::raw('COUNT(collection_access.collection_id) as total_access')
        );

        $type = null;
        if ($request->type == 'buku') {
            $type = 1;
        } else if ($request->type == 'partitur') {
            $type = 2;
        } else if ($request->type == 'peta') {
            $type = 3;
        } else if ($request->type == 'serial') {
            $type = 4;
        } else if ($request->type == 'audio' || $request->type == 'musik') {
            $type = 5;
        } else if ($request->type == 'film' || $request->type == 'video') {
            $type = 6;
        }


        if ($type) {
            $collection->where('collections.type', $type);
        }

        if ($request->title) {
            $collection->where('collections.title', 'like', "%$request->title%");
        }

        if ($request->author) {
            $author = Author::where('fullname', 'like', "%$request->author%")->pluck('id');
            $collection->whereHas('collectionContributor', function ($query) use ($request, $author) {
                $query->whereIn('author_id', $author);
            });
        }

        if ($request->publisher_name) {
            $publisher = Publisher::where('name', 'like', "%$request->publisher_name%")->pluck('id');
            $collection->whereIn('publisher_id', $publisher);
        }

        if ($request->publication_year) {
            $collection->where('publication_year', $request->publication_year);
        }

        if ($request->category) {

            $category_id = [];
            foreach ($request->category as $key => $value) {
                $category = Category::where('name', 'like', "%$value%")->pluck('id');
                foreach ($category as $value) {
                    array_push($category_id, $value);
                }
            }

            $collection->whereHas('collectionCategory', function ($query) use ($request, $category_id) {
                $query->whereIn('category_id', $category_id);
            });
        }

        if ($request->subject) {

            $subject_id = [];

            foreach ($request->subject as $key => $value) {
                $subject = Subject::where('name', 'like', "%$value%")->pluck('id');
                foreach ($subject as $value) {
                    array_push($subject_id, $value);
                }
            }


            $collection->whereHas('collectionSubject', function ($query) use ($request, $subject_id) {
                $query->whereIn('subject_id', $subject_id);
            });
        }

        $collection
            ->leftJoin('collection_access', 'collections.id', 'collection_access.collection_id')
            ->leftJoin('publishers', 'publishers.id', 'collections.publisher_id')
            ->where('collections.parent_id', 0)
            ->where('collections.status', 2);

        $total = $collection->count();

        $data = $collection->limit($numOfPage)
            ->skip(($page - 1) * $numOfPage)
            ->groupBy('collections.id')
            ->get();

        $data = $data->map(function ($item) {
            $response = $this->createResponseCollection($item);
            $response['collection_id'] = $item->id;
            $response['type'] = $item->type();
            $response['title'] = $item->title;
            $response['acces_rights'] = $item->access;
            $response['total_access'] = $item->total_access;

            return $response;
        });

        $response = [
            'data'          => $data,
            'page'          => $page,
            'total'         => $total,
            'num_of_page'   => $numOfPage
        ];

        return response()->json($response);
    }

    public function favorite(Request $request)
    {

        $favorite = CollectionFavourite::pluck('collection_id');

        $page = isset($request->page) ? $request->page : 1;
        $numOfPage = isset($request->num_of_page) ? $request->num_of_page : 10;

        $collection = Collection::select(
            'collections.type',
            'collections.title',
            'collections.code',
            'collections.deposit',
            'collections.id',
            'collections.access',
            'collections.preview',
            'collections.publication_year',
            'publishers.name as publsiher_name',
            DB::raw('COUNT(collection_access.collection_id) as total_access')
        )
            ->leftJoin('collection_access', 'collections.id', 'collection_access.collection_id')
            ->where('collections.parent_id', 0)
            ->where('collections.status', 2)
            ->whereIn('collection_id', $favorite);


        $total = $collection->count();

        $data = $collection
            ->limit($numOfPage)
            ->skip(($page - 1) * $numOfPage)
            ->groupBy('collections.id')
            ->get();

        $data = $data->map(function ($item) {
            $response = $this->createResponseCollection($item);
            $response['collection_id'] = $item->id;
            $response['type'] = $item->type();
            $response['title'] = $item->title;
            $response['acces_rights'] = $item->access;
            $response['total_access'] = $item->total_access;

            return $response;
        });

        $response = [
            'data'          => $data,
            'page'          => $page,
            'total'         => $total,
            'num_of_page'   => $numOfPage
        ];

        return response()->json($response);
    }

    public function findOne(Request $request, $id)
    {

        $collection = Collection::select(
            'collections.type',
            'collections.title',
            'collections.code',
            'collections.deposit',
            'collections.id',
            'collections.access',
            'collections.preview',
            'collections.description',
            'collections.publication_year',
            'publishers.name as publsiher_name',
            DB::raw('COUNT(collection_access.collection_id) as total_access')
        )
            ->leftJoin('collection_access', 'collections.id', 'collection_access.collection_id')
            ->leftJoin('publishers', 'publishers.id', 'collections.publisher_id')
            ->where('collections.status', 2)
            ->where('collections.id', $id)
            ->first();

        if (!$collection) {
            return response()->json([
                'message'    => 'Koleksi tidak ditemukan',
                'status'    => 'Not Found'
            ], 404);
        }


        $response = $this->createResponseCollection($collection);
        $response['description']    = $collection->description;

        if ($collection->type == 1) {
            $response['jumlahHalaman']  = $this->getTotalFilePDF($collection);
            $response['isbn']           = $collection->code;
        } else if ($collection->type == 2) {
            $response['jumlahHalaman']  = $this->getTotalFilePDF($collection);
            $response['ismn']           = $collection->code;
        } else if ($collection->type == 3) {
            $response['jumlahHalaman']  = $this->getTotalFilePDF($collection);
            $response['isbn']           = $collection->code;
            $reponse['scale']           = isset($collection->physicalDescription()->scale) ? $collection->physicalDescription()->scale : null;
        } else if ($collection->type == 5) {
            $media = $collection->CollectionMedia()->where('type', 6)->first();
            $response['isrc']               = $collection->code;
            $reponse['duration']            = isset($collection->physicalDescription()->duration) ? $collection->physicalDescription()->duration : null;
            $response['link_watermark']     = $media == null ? null : url('api/collection/media/audio/' . $this->getEncryptFile($media->link));
        } else if ($collection->type == 6) {
            $media = $collection->CollectionMedia()->where('type', 9)->first();
            $response['isrc']               = $collection->code;
            $reponse['duration']            = isset($collection->physicalDescription()->duration) ? $collection->physicalDescription()->duration : null;
            $response['link_watermark']     = $media == null ? null : url('api/collection/media/audiovisual/' . $this->getEncryptFile($media->link));
        }

        $response['contributors']    = $this->getContributors($collection);
        $response['subjects']       = $this->getSubjects($collection);
        $response['categories']     = $this->getCategories($collection);

        if ($request->pemustaka_id) {
            $response['is_favorite'] = $this->getFavorites($request->pemustaka_id, $collection->id);
        } else {
            $response['is_favorite'] = 0;
        }

        return response()->json($response);
    }

    public function serial(Request $request, $id)
    {

        $collection = Collection::select(
            'collections.type',
            'collections.title',
            'collections.code',
            'collections.deposit',
            'collections.id',
            'collections.access',
            'collections.preview',
            'collections.publication_year',
            'publishers.name as publsiher_name',
            DB::raw('COUNT(collection_access.collection_id) as total_access')
        )
            ->leftJoin('collection_access', 'collections.id', 'collection_access.collection_id')
            ->leftJoin('publishers', 'publishers.id', 'collections.publisher_id')
            ->where('collections.id', $id)
            ->where('collections.parent_id', 0)
            ->where('collections.type', 4)
            ->where('collections.status', 2)
            ->first();

        if (!$collection) {
            return response()->json([
                'message'   => 'Koleksi tidak ditemukan',
                'status'    => 'Not Found'
            ], 404);
        }

        $response                   = $this->createResponseCollection($collection);
        $response['isbn']           = $collection->code;
        $response['serial']         = $this->getSerialItem($request, $collection->id);
        $response['contributors']   = $this->getContributors($collection);
        $response['subjects']       = $this->getSubjects($collection);
        $response['categories']     = $this->getCategories($collection);

        return response()->json($response);
    }

    public function serialDetail(Request $request, $id, $serialId)
    {

        $collection = Collection::select(
            'collections.type',
            'collections.title',
            'collections.code',
            'collections.deposit',
            'collections.id',
            'collections.access',
            'collections.preview',
            'collections.publication_year',
            'publishers.name as publsiher_name',
            DB::raw('COUNT(collection_access.collection_id) as total_access')
        )
            ->leftJoin('collection_access', 'collections.id', 'collection_access.collection_id')
            ->leftJoin('publishers', 'publishers.id', 'collections.publisher_id')
            ->where('collections.id', $serialId)
            ->where('collections.parent_id', $id)
            ->where('collections.status', 2)
            ->first();

        if (!$collection) {
            return response()->json([
                'message'   => 'Koleksi tidak ditemukan',
                'status'    => 'Not Found'
            ], 404);
        }

        $response = $this->createResponseCollection($collection);
        $response['jumlahHalaman']  = $this->getTotalFilePDF($collection);
        $response['contributors']    = $this->getContributors($collection);
        $response['subjects']       = $this->getSubjects($collection);
        $response['categories']     = $this->getCategories($collection);

        if ($request->pemustaka_id) {
            $response['is_favorite'] = $this->getFavorites($request->pemustaka_id, $collection->id);
        } else {
            $response['is_favorite'] = 0;
        }

        return response()->json($response);
    }

    private function createResponseCollection($collection)
    {
        return [
            'type'                  => $collection->type(),
            'title'                 => $collection->title,
            'publisher'             => $collection->publsiher_name,
            'publishYear'           => $collection->publication_year,
            'deposit'               => $collection->deposit,
            'total_access'          => $collection->total_access,
        ];
    }

    private function getTotalFilePDF($collection)
    {
        $media          = CollectionMedia::where('collection_id', $collection->id)
            ->where('type', 3)->first();
        $file           = $media ? $media->jsonParse() : [];
        return count($file);
    }

    private function getSerialItem(Request $request, $parentId)
    {

        $page = isset($request->page) ? $request->page : 1;
        $numOfPage = isset($request->num_of_page) ? $request->num_of_page : 10;

        $collection = Collection::where('collections.parent_id', $parentId)
            ->where('status', 2);


        $total      = $collection->count();

        $collection = $collection->limit($numOfPage)
            ->skip(($page - 1) * $numOfPage)
            ->get();

        $items = $collection->map(function ($item) {
            return [
                'id'                    => $item->id,
                'title'                 => $item->title,
                'edition'               => $item->edition,
                'deposit'               => $item->deposit,
            ];
        });

        return [
            'data'          => $items,
            'page'          => $page,
            'total'         => $total,
            'num_of_page'   => $numOfPage
        ];
    }

    private function getContributors($collection)
    {
        $contributors = [];

        foreach ($collection->collectionContributor as $cc) {
            $contributors[] = [
                "name"      => $cc->author->fullname,
                "role"      => $cc->contributor->name,
                "yob"       => $cc->author->year_of_birth,
                "yod"       => $cc->author->year_of_death,
                "title"     => $cc->author->title
            ];
        }

        return $contributors;
    }

    private function getSubjects($collection)
    {
        $subjects = [];

        foreach ($collection->collectionSubject as $s) {
            $subjects[] = $s->subject->name;
        }

        return $subjects;
    }

    private function getCategories($collection)
    {
        $categories = [];

        foreach ($collection->collectionCategory as $s) {
            $categories[] = $s->category->name;
        }

        return $categories;
    }

    private function getFavorites($pemustakaId, $collectionId)
    {
        $favorite = CollectionFavourite::where('pemustaka_id', $pemustakaId)
            ->where('collection_id', $collectionId)
            ->where('status', 2)
            ->first();

        if ($favorite) {
            return 1;
        }

        return 0;
    }

    private function getEncryptFile($link)
    {
        return GeneralHelper::encryptString($link);
    }
}
