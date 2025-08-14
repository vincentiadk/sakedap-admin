<?php

namespace App\Http\Controllers\Publisher;

use App\Models\Collection;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
use App\Jobs\PDFToImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CollectionEditionController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function create(Request $request, $id)
    {
        $collection = Collection::find($id);
        $create     = Collection::create([
            'parent_id'    => $id,
            'publisher_id' => $collection->publisher_id,
            'type'         => 4,
            'edition'      => $request->edition_field,
            'deposit'      => GeneralHelper::depositCollection(),
            'copyright'    => 'Copyrights (c) ' . date('Y') . ' ' . $collection->publisher->name,
            'manual'       => 1,
            'date'         => $request->date_field,
            'status'       => 1,
            'received_at'  => date('Y-m-d H:i:s'),
            'edit_by'      => session('id'),
            'created_by'   => session('id'),
            'updated_by'   => session('id'),
            'validated_by' => session('id'),
            'validated_at' => date('Y-m-d H:i:s')
        ]);

        $cover    = $request->file('cover_field');
        $original = $request->file('original_field');

        $cover_filename = Str::uuid()->toString() . '.' . $cover->getClientOriginalExtension();
        $original_filename = Str::uuid()->toString() . '.' . $original->getClientOriginalExtension();

        $linkCollectionCover  = Storage::disk($this->location->location)->put('public/collection/serial/edition/cover/' . $create->id . '/' . $cover_filename, $cover);
        $dir_original = Storage::disk($this->location->location)->put('public/collection/serial/edition/original/' . $create->id . '/' . $original_filename, $original);

        CollectionMedia::insert([
            [
                'collection_id' => $create->id,
                'link'          => $linkCollectionCover,
                'size'          => File::size($cover),
                'extension'     => $cover->getClientOriginalExtension(),
                'mimes'         => File::mimeType($cover),
                'hash'          => md5_file($cover),
                'type'          => 1,
                'method'        => 3,
                'location_id'   => $this->location->id,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ],
            [
                'collection_id' => $create->id,
                'link'          => $dir_original,
                'size'          => File::size($original),
                'extension'     => $original->getClientOriginalExtension(),
                'mimes'         => File::mimeType($original),
                'hash'          => md5_file($original),
                'type'          => 2,
                'method'        => 3,
                'location_id'   => $this->location->id,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        ]);


        $job = new PDFToImage('serial/edition', $dir_original, $create->id);
        dispatch(($job)->onQueue('convert_pdf'));

        $cover_image = '<a href="' . asset(Storage::url($linkCollectionCover)) . '" data-lightbox="' . $linkCollectionCover . '" data-title="' . $linkCollectionCover . '"><img src="' . asset(Storage::url($linkCollectionCover)) . '" style="max-height:30px; max-width:30px;"></a>';

        $original_file = '<form method="GET" action="' . url('publisher/collection/stream_file_pdf') . '" target="_blank">
            <input type="hidden" name="csrf-token" value="' . csrf_token() . '">
            <input type="hidden" name="file_stream" value="' . $dir_original . '">
            <button type="submit" class="btn btn-success btn-sm">Lihat File</button>
        </form>';

        return response()->json([
            'id'             => $create->id,
            'date_field'     => date('d-m-Y', strtotime($request->date_field)),
            'cover_field'    => $cover_image,
            'original_field' => $original_file,
        ]);
    }

    public function destroy(Request $request)
    {
        $media = CollectionMedia::where('collection_id', $request->id);
        $link  = [];

        foreach ($media->get() as $m) {
            $link[] = $m->link;
        }

        Storage::delete($link);
        Collection::find($request->id)->update(['deposit' => null]);
        $collection = Collection::find($request->id)->delete();
        $media->delete();

        activity()
            ->performedOn($collection)
            ->causedBy(User::find(session('id')))
            ->log('Menghapus Edisi Serial ' . $collection->title);

        return response()->json(200);
    }
}
