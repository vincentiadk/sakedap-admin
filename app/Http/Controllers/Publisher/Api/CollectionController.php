<?php

namespace App\Http\Controllers\Publisher\Api;

use DB;
use Auth;
use App\Models\Solr;
use App\Models\User;
use App\Models\Author;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Location;
use App\Jobs\PDFToImage;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\Contributor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\CollectionMedia;
use App\Models\PublisherAccess;
use App\Models\CollectionSubject;
use App\Models\CollectionCategory;
use App\Http\Controllers\Controller;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendMailCollectionSubmitted;
use Illuminate\Support\Facades\Validator;
use LynX39\LaraPdfMerger\Facades\PdfMerger;

class CollectionController extends Controller
{

    private $publisher;
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function get(Request $request)
    {
        $auth         = User::find(session('user_id'));
        $publisher_id = $auth->publisher->getPublisherByGroup();
        $collection   = Collection::whereIn('publisher_id', $publisher_id);
        $code_type;
        $type = $request->type;
        if ($type == 'buku') {
            $code_type = 1;
        } else if ($type == 'partitur') {
            $code_type = 2;
        } else if ($type == 'peta') {
            $code_type = 3;
        } else if ($type == 'audio') {
            $code_type = 5;
        } else if ($type == 'film') {
            $code_type = 6;
        } else {
            $code_type = null;
        }

        if ($code_type != null) {
            $collection->where('type', $code_type);
        }

        if ($request->isbn != null) {
            $collection->where('code', 'LIKE', "%$request->isbn%");
        }

        if ($request->received == "0") {
            $collection->whereNull('received_at');
        } else {
            if ($request->received_month != null) {
                $collection->whereMonth('received_at', $request->received_month);
            }

            if ($request->received_day != null) {
                $collection->whereDay('received_at', $request->received_day);
            }

            if ($request->received_year != null) {
                $collection->whereYear('received_at', $request->received_year);
            }
        }

        return $collection->paginate(10);
    }

    public function create(Request $request)
    {
        $code_type;
        $type = $request->type;
        $auth = User::find(session('user_id'));

        if (strtolower($type) == 'buku') {
            $code_type = 1;
        } else if (strtolower($type) == 'partitur') {
            $code_type = 2;
        } else if (strtolower($type) == 'peta') {
            $code_type = 3;
        } else if (strtolower($type) == 'audio') {
            $code_type = 5;
        } else if (strtolower($type) == 'film') {
            $code_type = 6;
        } else {
            $code_type = null;
        }

        if ($code_type == null) {
            return response()->json([
                'message'   => 'type koleksi tidak ditemukan!',
                'code'      => 404
            ], 404);
        }
        if ($request->code != "") {
            //\Log::info($request->code);
            \Log::info(Collection::where('code', $request->code)->count());
            if (Collection::where('code', $request->code)->count() > 0) {
                return response()->json([
                    'message'   => $request->code . ' sudah pernah diserahkan!',
                    'code'      => 404
                ], 404);
            }
        }
        if ($code_type == 1 && $request->code != "") {
            return $this->createBookISBNCollection($request, $auth->id);
        } else {
            $parent = Collection::create([
                'publisher_id'     => $auth->publisher->id,
                'title'            => $request->title,
                'slug'             => Str::slug($request->title, '-'),
                'type'             => $code_type,
                'deposit_head_id'  => $code_type,
                'publication_month' => $request->publication_month,
                'publication_year' => $request->publication_year,
                'preview'          => $request->preview,
                'description'      => $request->description,
                'city_id'          => $auth->publisher->city_id,
                'price'            => $request->price,
                'manual'           => 0,
                'deposit'          => GeneralHelper::depositCollection(),
                'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $auth->publisher->name,
                'status'           => 1,
                'created_by'       => $auth->id,
                'updated_by'       => $auth->id,
                'access'           => $request->access,
            ]);

            $this->createAdditonalInfo($request, $parent->id);
        }

        if ($type == 'buku') {
            $this->uploadCollection($parent->id, $request->file_cover, $request->file_original, 'book');
        } else if ($type == 'partitur') {
            $this->uploadCollection($parent->id, $request->file_cover, $request->file_original, 'partitur');
        } else if ($type == 'peta') {
            $this->uploadCollection($parent->id, $request->file_cover, $request->file_original, 'map');
        } else if ($type == 'audio') {
            $this->uploadCollection($parent->id, $request->file_cover, $request->file_original, 'audio');
        } else if ($type == 'film') {
            $this->uploadCollection($parent->id, $request->file_cover, $request->file_original, 'film');
        }

        $collection = Collection::find($parent->id);
        $params = [
            'user_id'   => $request->user_id,
            'publisher' => $collection->publisher->name,
            'title'     => $collection->title
        ];

        $job = new SendMailCollectionSubmitted($params);
        dispatch(($job)->onQueue('notification'));

        return response()->json([
            'message'   => $request->code . ' Berhasil diunggah!',
            'code'      => 200
        ], 200);
    }

    private function createBookISBNCollection(Request $request, $user_id)
    {
        $auth             = User::find($user_id);
        $code_clean             = str_replace('-', '', $request->code);
        $isbn             = Solr::data('isbn', 'complete', ['code' => '"' . $code_clean . '"']);
        $publisher_access = PublisherAccess::where('code_system', $isbn[0]['kd_penerbit'])->first();
        $publisher_code = $publisher_access->code_system;
        $publisher_real = Publisher::where('code_system', $publisher_code)->first();
        //\Log::info($publisher_access);
        //$publisher_auth   = PublisherAccess::where('publisher_id', $auth->publisher->id)->first();
        if (count($isbn) < 1) {
            return response()->json([
                'message'   => 'kode ISBN tidak ditemukan',
                'code'      => 404
            ], 404);
        }

        if ($isbn[0]['jenis'] == 'cetak') {
            return response()->json([
                'message'   => 'Bukan ISBN elektronik!',
                'code'      => 400
            ], 400);
        }
        if (!$auth->publisher->checkAccess($publisher_real->id, $auth->publisher->id)) {
            return response()->json([
                'message'   => 'Tidak Ada Akses',
                'code'      => 400
            ], 400);
        }

        $collection = Collection::select('id')->where('code', $request->code)->first();
        if ($collection) {
            return response()->json([
                'message'   => 'No. ISBN sudah pernah diserahkan',
                'code'      => 400
            ], 400);
        }



        $type_book = 1;
        $code_kdt  = $isbn[0]['kd_penerbit_dtl'];
        $code_type = 1;

        $parent = Collection::create([
            'publisher_id'     => $publisher_real->id,
            'title'            => $isbn[0]['title'],
            'slug'             => Str::slug($isbn[0]['title'], '-'),
            'type'             => $code_type,
            'type_book'        => $type_book,
            'deposit_head_id'  => $code_type,
            'kepeng'           => $isbn[0]['kepeng'],
            'ddc'              => $isbn[0]['call_number'],
            'edition'          => $isbn[0]['edisi'],
            'series'           => $isbn[0]['seri'],
            'price'            => $request->price,
            'code'             => $request->code,
            'code_type'        => $code_type,
            'code_kdt'         => $code_kdt,
            'publication_year' => $isbn[0]['tahun_terbit'],
            'publication_month' => $request->publication_month,
            'preview'          => $request->preview,
            'description'      => $request->description,
            'city_id'          => $publisher_real->city_id,
            'manual'           => 0,
            'deposit'          => GeneralHelper::depositCollection(),
            'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $auth->publisher->name,
            'status'           => 1,
            'created_by'       => $auth->id,
            'updated_by'       => $auth->id,
            'access'           => $request->access,
        ]);

        $this->createAdditonalInfo($request, $parent->id);
        $this->uploadCollection($parent->id, $request->file_cover, $request->file_original, 'book');

        if ($isbn[0]['subjek'] != null) {
            $this->createSubject($parent->id, $isbn[0]['subjek']);
        }
    }


    private function createAdditonalInfo(Request $request, $collectionId)
    {
        try {
            if ($request->has('category')) {
                foreach ($request->category as $cc) {
                    $category = Category::updateOrCreate([
                        'slug'      => Str::slug($cc),
                        'type'      => 1
                    ], [
                        'slug'      => Str::slug($cc),
                        'type'      => 1,
                        'name'      => $cc
                    ]);

                    CollectionCategory::create([
                        'collection_id' => $collectionId,
                        'category_id'   => $category->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage() . " . Category is incorrect format.",
                'code'      => 404
            ], 404);
        }
        try {
            if ($request->has('contributor')) {
                foreach ($request->contributor as $key => $item) {

                    $contributor = Contributor::updateOrCreate([
                        'slug'          => Str::slug($item['name'], '-'),
                        'type'          => 1
                    ], [
                        'slug'          => Str::slug($item['name'], '-'),
                        'type'          => 1,
                        'name'          => $item['name']
                    ]);

                    $author = Author::updateOrCreate([
                        'slug'          => Str::slug($item['author_fullname'], '-')
                    ], [
                        'title'         => $item['author_title'],
                        'fullname'      => $item['author_fullname'],
                        'year_of_birth' => $item['author_year_of_birth'],
                        'year_of_death' => $item['author_year_of_death']
                    ]);

                    CollectionContributor::create([
                        'collection_id'  => $collectionId,
                        'contributor_id' => $contributor->id,
                        'author_id'      => $author->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage() .  " . Contributor is incorrect format.",
                'code'      => 400
            ], 400);
        }
    }

    private function createSubject($collectionId, $cs)
    {
        try {
            $subjectCheck = Subject::updateOrCreate([
                'slug' => Str::slug($cs, '-')
            ], [
                'name' => $cs
            ]);

            $subject = Subject::where('name', $cs)
                ->where('slug', Str::slug($cs, '-'))
                ->first();

            CollectionSubject::create([
                'collection_id' => $collectionId,
                'subject_id'    => $subject->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage() .  " . Subject is incorrect format.",
                'code'      => 400
            ], 400);
        }
    }

    private function uploadCollection($collectionId, $cover, $original, $type)
    {
        try {
            if ($type == 'book') {
                if (str_contains($cover, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $contents = @file_get_contents($cover, false, stream_context_create($arrContextOptions));
                } else {
                    $contents = @file_get_contents($cover);
                }

                $coverName = Str::random(32) . '.png';
                $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/book/cover/' . $collectionId . "/" . $coverName, $contents);


                if (str_contains($original, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $originalContents = @file_get_contents($original, false, stream_context_create($arrContextOptions));
                } else {
                    $originalContents = @file_get_contents($original);
                }

                $originalName = Str::random(32) . '.pdf';
                $dir_original =  Storage::disk($this->location->location)->put('public/collection/book/original/' . $collectionId . '/' . $originalName,  $originalContents);


                $job = new PDFToImage('book', 'public/collection/book/original/' . $collectionId . '/' . $originalName, $collectionId);
                dispatch(($job)->onQueue('convert_pdf'));
            } else if ($type == 'partitur') {

                if (str_contains($cover, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $contents = @file_get_contents($cover, false, stream_context_create($arrContextOptions));
                } else {
                    $contents = @file_get_contents($cover);
                }

                $coverName = Str::random(32) . '.png';
                $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/partitur/cover/' . $collectionId . '/' . $coverName, $contents);

                if (str_contains($original, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $originalContents = @file_get_contents($original, false, stream_context_create($arrContextOptions));
                } else {
                    $originalContents = @file_get_contents($original);
                }

                $originalName = Str::random(32) . '.pdf';
                $dir_original =  Storage::disk($this->location->location)->put('public/collection/partitur/original/' . $collectionId . '/' . $originalName, $originalContents);

                $job = new PDFToImage('partitur', $dir_original, $collectionId);
                dispatch(($job)->onQueue('convert_pdf'));
            } else if ($type == 'map') {

                if (str_contains($cover, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $contents = @file_get_contents($cover, false, stream_context_create($arrContextOptions));
                } else {
                    $contents = @file_get_contents($cover);
                }

                $coverName = Str::random(32) . '.png';
                $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/map/cover/' . $collectionId . '/' . $coverName, $contents);

                if (str_contains($original, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $originalContents = @file_get_contents($original, false, stream_context_create($arrContextOptions));
                } else {
                    $originalContents = @file_get_contents($original);
                }

                $originalName = Str::random(32) . '.pdf';
                $dir_original =  Storage::disk($this->location->location)->put('public/collection/map/original/' . $collectionId . '/' . $originalName, $originalContents);

                $job = new PDFToImage('map', $dir_original, $collectionId);
                dispatch(($job)->onQueue('convert_pdf'));
            } else if ('audio') {

                if (str_contains($cover, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $contents = @file_get_contents($cover, false, stream_context_create($arrContextOptions));
                } else {
                    $contents = @file_get_contents($cover);
                }

                $coverName = Str::random(32) . '.png';
                $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/' . $type . '/cover/' . $collectionId . '/' . $coverName, $contents);

                if (str_contains($original, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $originalContents = @file_get_contents($original, false, stream_context_create($arrContextOptions));
                } else {
                    $originalContents = @file_get_contents($original);
                }

                $originalName = Str::random(32) . '.wav';
                $dir_original =  Storage::disk($this->location->location)->put('public/collection/' . $type . '/original/' . $collectionId . '/' . $originalName, $originalContents);
            } else if ('film') {

                if (str_contains($cover, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $contents = @file_get_contents($cover, false, stream_context_create($arrContextOptions));
                } else {
                    $contents = @file_get_contents($cover);
                }

                $coverName = Str::random(32) . '.png';
                $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/' . $type . '/cover/' . $collectionId . '/' . $coverName, $contents);

                if (str_contains($original, "https://")) {
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    $originalContents = @file_get_contents($original, false, stream_context_create($arrContextOptions));
                } else {
                    $originalContents = @file_get_contents($original);
                }

                $originalName = Str::random(32) . '.mp4';
                $dir_original =  Storage::disk($this->location->location)->put('public/collection/' . $type . '/original/' . $collectionId . '/' .  $originalName, $originalContents);
            }

            if ($type == 'audio') {
                $originalType = 4;
            } else if ($type == 'video') {
                $originalType = 7;
            } else {
                $originalType = 2;
            }

            CollectionMedia::insert([
                [
                    'collection_id' => $collectionId,
                    'link'          => 'public/collection/' . $type . '/cover/' . $collectionId . '/' . $coverName,
                    'size'          => Storage::disk($this->location->location)->size('public/collection/' . $type . '/cover/' . $collectionId . '/' . $coverName),
                    'extension'     => 'png',
                    'mimes'         => Storage::disk($this->location->location)->mimeType('public/collection/' . $type . '/cover/' . $collectionId . '/' . $coverName),
                    'hash'          => md5_file($cover),
                    'type'          => 1,
                    'method'        => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'location_id'   => $this->location->id
                ],
                [
                    'collection_id' => $collectionId,
                    'link'          => 'public/collection/' . $type . '/cover/' . $collectionId . '/' . $originalName,
                    'size'          => Storage::disk($this->location->location)->size('public/collection/' . $type . '/original/' . $collectionId . '/' . $originalName),
                    'extension'     => 'pdf',
                    'mimes'         => Storage::disk($this->location->location)->mimeType('public/collection/' . $type . '/original/' . $collectionId . '/' . $originalName),
                    'hash'          => md5_file($original),
                    'type'          => $originalType,
                    'method'        => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'location_id'   => $this->location->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'code'      => 400
            ], 400);
        }
    }
}
