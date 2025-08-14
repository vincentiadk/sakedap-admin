<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Collection;
use App\Models\CollectionMedia;
use App\Helper\GeneralHelper;
use App\Models\Location;


class SerialEditionController extends Controller
{

    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index($id)
    {
        $data = [
            'title'      => 'Koleksi Edisi Serial',
            'collection' => Collection::find($id),
            'content'    => 'publisher.serial.edition'
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request, $parent_id)
    {
        $whereLike = [
            'publisher_id',
            'title',
            'deposit',
            'publication_year',
            'content'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $totalData = Collection::where('parent_id', $parent_id)
            ->count();
        if (empty($search)) {
            $queryData = Collection::where('parent_id', $parent_id)
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where('parent_id', $parent_id)
                ->count();
        } else {
            $queryData = Collection::where('parent_id', $parent_id)
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('deposit', 'like', "%{$search}%")
                        ->orWhere('publication_year', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where('parent_id', $parent_id)
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('deposit', 'like', "%{$search}%")
                        ->orWhere('publication_year', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $file = $val->collectionMedia()->where('collection_id', $val->id)->first();
                $response['data'][] = [
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>',
                    $val->deposit,
                    $val->publication_year,
                    '<a href="' . url('publisher/serial/edition/stream_file/' . $val->id) . '" class="text-primary" target="_blank">Lihat File</a>',
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                ];
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publisher_id'     => 'required',
            'title'            => 'required',
            'publication_year' => 'required|date_format:Y',
            'edition'          => 'required',
            'original'         => 'required|file|max:500000|mimes:pdf'
        ], [
            'publisher_id.required'        => 'Harap memilih penerbit',
            'title.required'               => 'Judul wajib di isi!',
            'publication_year.required'    => 'Tahun terbit wajib di isi!',
            'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
            'edition.required'             => 'Edisi wajib di isi!',
            'original.required'            => 'File konten wajib di isi!',
            'original.image'               => 'File konten berupa file image!',
            'original.max'                 => 'File konten maksimal 500MB!',
            'original.mimes'               => 'File konten harus bertipe pdf!'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Collection::create([
                'publisher_id'     => $request->publisher_id,
                'parent_id'        => $request->parent_id,
                'title'            => $request->title,
                'slug'             => Str::slug($request->title, '-'),
                'type'             => 4,
                'deposit'          => GeneralHelper::depositCollection(),
                'publication_year' => $request->publication_year,
                'edition'          => $request->edition
            ]);

            if ($create) {
                $link_original = Storage::disk($this->location->location)->put('public/serial/original', $request->file('original'));
                CollectionMedia::create([
                    'collection_id' => $create->id,
                    'link'          => $link_original,
                    'size'          => File::size($request->file('original')),
                    'extension'     => $request->file('original')->getClientOriginalExtension(),
                    'mimes'         => File::mimeType($request->file('original')),
                    'hash'          => md5_file($request->file('original')),
                    'type'          => 'Original',
                    'method'        => 4
                ]);

                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal ditambahkan'
                ];
            }
        }

        return response()->json($response);
    }

    public function show($id)
    {
        $data = Collection::find($id);
        return response()->json([
            'publisher_id'     => $data->publisher->id,
            'publisher_name'   => $data->publisher->name,
            'title'            => $data->title,
            'publication_year' => $data->publication_year,
            'edition'          => $data->edition
        ]);
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::find($id);
        $validator  = Validator::make($request->all(), [
            'publisher_id'     => 'required',
            'title'            => 'required',
            'publication_year' => 'required|date_format:Y',
            'edition'          => 'required',
            'original'         => 'file|max:500000|mimes:pdf'
        ], [
            'publisher_id.required'        => 'Harap memilih penerbit',
            'title.required'               => 'Judul wajib di isi!',
            'publication_year.required'    => 'Tahun terbit wajib di isi!',
            'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
            'edition.required'             => 'Edisi wajib di isi!',
            'original.image'               => 'File konten berupa file image!',
            'original.max'                 => 'File konten maksimal 500MB!',
            'original.mimes'               => 'File konten harus bertipe pdf!'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $update = Collection::where('id', $id)->update([
                'publisher_id'     => $request->publisher_id,
                'parent_id'        => $request->parent_id,
                'title'            => $request->title,
                'slug'             => Str::slug($request->title, '-'),
                'type'             => 4,
                'publication_year' => $request->publication_year,
                'edition'          => $request->edition
            ]);

            if ($update) {
                if ($request->has('original')) {

                    $collectionMedia = CollectionMedia::where('collection_id', $id)->where('type', 2)->first();
                    Storage::disk($collectionMedia->location->location)->delete($collectionMedia->link);

                    $original = $request->file('original');
                    $path = Storage::disk($this->location->location)->put('public/serial/original/' . $id, $original);

                    CollectionMedia::create([
                        'collection_id' => $create->id,
                        'link'          => $path,
                        'size'          => File::size($original),
                        'extension'     => $original->getClientOriginalExtension(),
                        'mimes'         => File::mimeType($original),
                        'hash'          => md5_file($original),
                        'type'          => 2,
                        'method'        => 4,
                        'location_id'   => $this->location->location,
                    ]);
                }

                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal diupdate'
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy($id)
    {
        $destroy = Collection::where('id', $id)->delete();
        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }

    public function streamFile($id)
    {
        $media = CollectionMedia::find($id);
        $file  = asset(Storage::disk($media->location->location)->url($media->link));

        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment;filename=' . $file->hash . '.pdf');

        readfile($file);
    }
}
