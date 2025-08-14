<?php

namespace App\Http\Controllers\Frontend;

use App\Models\City;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\DepositHead;
use Illuminate\Http\Request;
use App\Models\CollectionCopy;
use App\Models\CollectionMedia;
use App\Models\CollectionSubject;
use App\Models\CollectionCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index(Request $request)
    {

        $typeSearch = $request->input('type-search');

        $depositHead = DepositHead::get();

        $typeDeposit = [];
        foreach ($depositHead as $value) {
            $typeDeposit[$value->category][] = $value->id;
        }

        $krd = $typeDeposit['KRD'];
        $kckra = array_merge($typeDeposit['KC'], $typeDeposit['KRA']);

        $collection = Collection::select('id', 'title', 'slug', 'type', 'publication_year')
            ->where('parent_id', 0)->where(function ($query) use ($krd, $kckra) {
                $query->where(function ($query) use ($krd) {
                    $query->where('status', 2)->whereIn('type', $krd);
                })
                    ->orWhere(function ($query) use ($kckra) {
                        $query->whereIn('type', $kckra)->where(function ($query) {
                            $query->whereHas('collectionCopy', function ($query) {
                                $query->where('availability', 0);
                            })->orWhereHas('collectionEditionCopy', function ($query) {
                                $query->where('availability', 0);
                            });
                        });
                    });
            });
        // dd($collection->toSql());

        if ($request->input('query')) {
            $query = '%' . $request->input('query') . '%';
            if ($typeSearch == 'author') {
                $author = Author::where('fullname', 'like', $query)->pluck('id');
                $collection->whereHas('collectionContributor', function ($query) use ($request, $author) {
                    $query->whereIn('author_id', $author);
                });
            } else if ($typeSearch == 'category') {
                $category = Category::where('name', 'like', $query)->pluck('id');
                $collection->whereHas('collectionCategory', function ($query) use ($request, $category) {
                    $query->whereIn('category_id', $category);
                });
            } else if ($typeSearch == 'publisher') {
                $publisher = Publisher::where('name', 'like', $query)->pluck('id');
                $collection->whereIn('publisher_id', $publisher);
            } else if ($typeSearch == 'subject') {
                $subject = Subject::where('name', 'like', $query)->pluck('id');
                $collection->whereHas('collectionSubject', function ($query) use ($request, $subject) {
                    $query->whereIn('subject_id', $subject);
                });
            } else if ($typeSearch == 'publication_place') {
                $city = City::where('name', 'like', $query)
                    ->orWhereHas('province', function ($q) use ($query) {
                        $q->where('name', 'like', $query);
                    })->pluck('id');
                $collection->whereIn('city_id', $city);
            } else if ($typeSearch == 'code') {
                $collection->where(function ($q) use ($query) {
                    $q->whereRaw("REPLACE(code, '-', '') like ? ", "$query")
                        ->orWhere('code', 'like', "$query");
                });
            } else {
                $collection->where("$typeSearch", 'like',  $query);
            }
        }

        if ($request->input('category')) {
            $category = Category::where('slug', $request->input('category'))->first();
            $collection->whereHas('collectionCategory', function ($query) use ($request, $category) {
                $query->where('category_id', $category->id);
            });
        }

        if ($request->input('type')) {
            $collection->where('type', $request->input('type'));
        }

        if ($request->input('subject')) {
            $subject = Subject::where('slug', $request->input('subject'))->first();
            $collection->whereHas('collectionSubject', function ($query) use ($request, $subject) {
                $query->where('subject_id', $subject->id);
            });
        }

        $collection = $collection
            ->orderBy('created_at', 'desc')
            ->paginate(21)->appends(request()->query());

        $topCategories = CollectionCategory::select(DB::raw('COUNT(collection_categories.category_id) as count_category, collection_categories.category_id as category_id'))
            ->where('collections.parent_id', 0)
            ->join('collections', 'collections.id', 'collection_categories.collection_id')
            ->groupBy('collection_categories.category_id')
            ->orderBy('count_category', 'DESC')
            ->take(10)
            ->pluck('category_id')->all();
        $categories = Category::whereIn('id', $topCategories)->get();

        $topSubjects = CollectionSubject::select(DB::raw('COUNT(collection_subjects.subject_id) as count_subject, collection_subjects.subject_id as subject_id'))
            ->groupBy('collection_subjects.subject_id')
            ->orderBy('count_subject', 'DESC')
            ->take(10)
            ->pluck('subject_id')->all();

        $subjects = Subject::whereIn('id', $topSubjects)->get();

        $totalCollection = DepositHead::selectRaw('count(DISTINCT collections.id) as total_collections, deposit_head.id, deposit_head.code, deposit_head.shape, deposit_head.category')
            ->leftJoin('collections', 'collections.deposit_head_id', '=', 'deposit_head.id')
            ->where('parent_id', 0)
            ->where(function ($query) use ($krd, $kckra) {
                $query->where(function ($query) use ($krd) {
                    $query->where('status', 2)->whereIn('type', $krd);
                })
                    ->orWhere(function ($query) use ($kckra) {
                        $query->whereIn('type', $kckra)->where(function ($query) {
                            $query->whereExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('collection_copies')
                                    ->whereColumn('collections.id', 'collection_copies.collection_id')
                                    ->where('collection_copies.availability', '0'); //tersedia
                            })->orWhereExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('collection_copies')
                                    ->join('collections as laravel_reserved_0', 'laravel_reserved_0.id', '=', 'collection_copies.collection_id')
                                    ->whereColumn('collections.id', 'laravel_reserved_0.parent_id')
                                    ->whereNull('laravel_reserved_0.deleted_at')
                                    ->where('collection_copies.availability', '0'); //tersedia
                            });
                        });
                    });
            })
            ->groupBy('deposit_head.id')
            ->orderBy('total_collections', 'desc')
            ->get();
        // dd($totalCollection);

        // dd($totalCollection);

        // $totalBook = Collection::where('type', 1)->where('status', 2)
        //     ->where('parent_id', 0)->count();
        // $totalPartitur = Collection::where('type', 2)->where('status', 2)
        //     ->where('parent_id', 0)->count();
        // $totalMap = Collection::where('type', 3)->where('status', 2)
        //     ->where('parent_id', 0)->count();
        // $totalSerial = Collection::where('type', 4)->where('status', 2)
        //     ->where('parent_id', 0)->count();
        // $totalAudio = Collection::where('type', 5)->where('status', 2)
        //     ->where('parent_id', 0)->count();
        // $totalVideo = Collection::where('type', 6)->where('status', 2)
        //     ->where('parent_id', 0)->count();

        $data = [
            'title'                     => 'Koleksi - Edeposit - National Library of Indonesia',
            'collection'              => $collection,
            'categories'              => $categories,
            'subjects'                => $subjects,
            'total_collections'         => $totalCollection,
            'content'                   => 'frontend.collection'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }

    public function detail($slug, $id)
    {
        $collection = Collection::where('slug', $slug)
            ->with(
                'collectionMedia',
                'collectionSubject',
                'collectionCategory',
                'collectionContributor',
                'collectionContributor.author',
                'collectionContributor.contributor',
                'publisher',
                'publisher.city',
                'depositHead',
                'collectionCopy',
            )
            ->where('id', $id)
            ->firstOrFail();
        // dd($collection);
        $edition = Collection::where('parent_id', $collection->id)->paginate(20);

        $data = [
            'title'                => $collection->title,
            'collection'         => $collection,
            'edition'            => $edition,
            'content'              => 'frontend.collection_detail'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }

    function datatableCopies(Request $request, $collection_id, $type = null)
    {
        $column = [
            'id',
            'sequence',
            'category',
            'question',
            'answer',
            'publish'
        ];

        $arrConditions = [
            '1' => 'Sangat Baik',
            '2' => 'Baik',
            '3' => 'Cukup',
            '4' => 'Rusak'
        ];

        $arrAvailability = [
            'tersedia',
            'dalam pengiriman ke pengelolaan',
            'sedang didayagunakan',
            'hilang',
            'rusak',
            'sedang diperbaiki',
            'sedang diolah',
            'masih di ekspedisi',
            'sedang dicek',
            'diterima pengelohan',
            'diterima tim kckr',
            'ditolak',
        ];

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $show_availability = ['0']; //tersedia

        if ($type !== 'all') {
            $total_data = CollectionCopy::where('collection_id', $collection_id)->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where('id', '>', 0)->count();

            $query_data = CollectionCopy::where('collection_id', $collection_id)->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    });
                }
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();

            $total_filtered = CollectionCopy::where('collection_id', $collection_id)->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    });
                }
            })
                ->count();

            $total_data_valid = CollectionCopy::where('id', '>', 0)->where('collection_id', $collection_id)->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where('deleted_at', null)->count();
        } else {
            $total_data = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->count();

            $query_data = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    });
                }
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();

            $total_filtered = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    });
                }
            })
                ->count();

            $total_data_valid = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where(function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->where('deleted_at', null)->count();
        }

        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {

                if (isset($arrConditions[$val->condition])) {
                    $conditions = $arrConditions[$val->condition];
                } else {
                    if (!empty($val->condition)) {
                        $conditions = $val->condition;
                    } else {
                        $conditions = '-';
                    }
                }
                if (isset($arrAvailability[$val->availability])) {
                    $availability = $arrAvailability[$val->availability];
                } else {
                    if (!empty($val->availability)) {
                        $availability = $val->availability;
                    } else {
                        $availability = '-';
                    }
                }
                if ($val->collection->depositHead->is_serial) {
                    $response['data'][] = [
                        $nomor,
                        $conditions,
                        $val->collection->edition,
                        $val->collection->price,
                        $availability,
                        $val->lib_location->library->name,
                        $val->lib_location->name,
                    ];
                } else {
                    $response['data'][] = [
                        $nomor,
                        $conditions,
                        $val->collection->price,
                        $availability,
                        $val->lib_location->library->name,
                        $val->lib_location->name,
                    ];
                }

                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($total_data <> FALSE) {
            $response['recordsTotal'] = $total_data;
        }

        $response['recordsFiltered'] = 0;
        if ($total_filtered <> FALSE) {
            $response['recordsFiltered'] = $total_filtered;
        }


        $response['recordsValid'] = $total_data_valid;

        return response()->json($response);
    }

    function datatableEditions(Request $request, $collection_id)
    {
        $column = [
            'id',
            'edition',
            'total_copy',
            'cover_image',
            'karantina',
            'action'
        ];

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $show_availability = ['1'];

        $total_data = Collection::where('parent_id', $collection_id)->where(function ($query) use ($show_availability) {
            $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            });
        })->count();

        $query_data = Collection::where('parent_id', $collection_id)->where(function ($query) use ($show_availability) {
            $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            });
        })->where(function ($query) use ($search) {
            if ($search) {
                $query->where('edition', 'like', "%$search%");
            }
        })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $total_filtered = Collection::where('parent_id', $collection_id)->where(function ($query) use ($show_availability) {
            $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            });
        })->where(function ($query) use ($search) {
            if ($search) {
                $query->where('edition', 'like', "%$search%");
            }
        })->count();

        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {
                // dd($val);
                // $name_cover = $val->depositHead->code;
                $cover = $val->collectionMedia->where('type', 1)->first();
                // dd($path_cover);
                if ($cover) {
                    $path_cover = $cover->link;
                    $cover_image = '<a target="_blank" class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->hash . '" data-title="' . $cover->hash . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';
                } else {
                    $cover_image = null;
                }

                $response['data'][] = [
                    $nomor,
                    $val->edition,
                    $val->start_publication_date . ' - ' . $val->end_publication_date,
                    $val->totalCopy(),
                    $cover_image,
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($total_data <> FALSE) {
            $response['recordsTotal'] = $total_data;
        }

        $response['recordsFiltered'] = 0;
        if ($total_filtered <> FALSE) {
            $response['recordsFiltered'] = $total_filtered;
        }

        $total_data_valid = Collection::where('parent_id', $collection_id)->where('id', '>', 0)->where('deleted_at', null)->count();

        $response['recordsValid'] = $total_data_valid;

        return response()->json($response);
    }

    public function loadImagePdf(Request $request)
    {
        $collection = Collection::find($request->collection_id);
        $data       = CollectionMedia::where('collection_id', $request->collection_id)->where('type', 3)->orderBy('created_at', 'DESC')->first();
        $file       = $data ? $data->jsonParse() : null;
        $total_file = 0;
        $image      = '';

        if ($file) {
            $image = $file[(int)$request->key - 1];
        }

        if ($collection->acccess != 1) {
            if ($collection->type == 4) {

                if (isset($collection->preview)) {
                    $preview = explode('-', $collection->preview);
                    $total_file = $preview[1];
                } else {
                    if (isset($collection->parent()->preview)) {
                        $preview = explode('-', $collection->parent()->preview);
                    } else {
                        $preview = 1;
                    }

                    $total_file = $preview[1];
                }
            } else {
                $preview = explode('-', $collection->preview);
                $total_file = $preview[1];
            }
        } else {
            $total_file = count($file);
        }

        if ($request->key > $total_file) {
            return response()->json([
                'image'      => null,
                'total_data' => $total_file
            ]);
        }

        return response()->json([
            'image'      => $image,
            'total_data' => $total_file
        ]);
    }

    public function detailIframe($id)
    {
        $collection = collection::findOrFail($id);

        $data = [
            'title'                => $collection->title,
            'collection'         => $collection,
            'content'              => 'frontend.collection_iframe'
        ];

        return view('frontend.layout.blank', ['data' => $data]);
    }
}
