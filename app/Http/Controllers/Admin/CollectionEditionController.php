<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
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

        $linkCollectionCover = Storage::disk($this->location->location)->put('public/collection/serial/edition/cover/' . $create->id, $cover);
        $dir_original        = Storage::disk($this->location->location)->put('public/collection/serial/edition/original/' . $create->id, $original);

        CollectionMedia::insert([
            [
                'collection_id' => $collection->id,
                'link'          => $linkCollectionCover,
                'size'          => File::size(Storage::disk($this->location->location)->path($linkCollectionCover)),
                'extension'     => pathinfo(Storage::disk($this->location->location)->path($linkCollectionCover), PATHINFO_EXTENSION),
                'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($linkCollectionCover)),
                'hash'          => md5_file(Storage::disk($this->location->location)->path($linkCollectionCover)),
                'type'          => 1,
                'method'        => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'location_id'   => $this->location->id
            ],
            [
                'collection_id' => $collection->id,
                'link'          => $dir_original,
                'size'          => File::size(Storage::disk($this->location->location)->path($dir_original)),
                'extension'     => pathinfo(Storage::disk($this->location->location)->path($dir_original), PATHINFO_EXTENSION),
                'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($dir_original)),
                'hash'          => md5_file(Storage::disk($this->location->location)->path($dir_original)),
                'type'          => 2,
                'method'        => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        ]);

        $cover_image = '<a href="' . asset(Storage::disk($this->location->location)->url($linkCollectionCover)) . '" data-lightbox="' . $linkCollectionCover . '" data-title="' . $linkCollectionCover . '"><img src="' . asset(Storage::disk($this->location->location)->url($linkCollectionCover)) . '" style="max-height:30px; max-width:30px;"></a>';

        $original_file = '<form method="GET" action="' . url('admin/collection/stream_file_pdf') . '" target="_blank">
            <input type="hidden" name="csrf-token" value="' . csrf_token() . '">
            <input type="hidden" name="file_stream" value="' . $linkCollectionCover . '">
            <button type="submit" class="btn btn-success btn-sm">Lihat File</button>
        </form>';

        activity('collections')
            ->performedOn($create)
            ->causedBy(session('id'))
            ->withProperties([
                'parent'           => $create->parent()->title,
                'penerbit'         => $create->publisher->name,
                'tipe'             => $create->type(),
                'edisi'            => $create->edition,
                'deposit'          => $create->deposit,
                'copyright'        => $create->copyright,
                'manual'           => 'Ya',
                'tanggal'          => $create->date,
                'status'           => $create->status(),
                'tanggal_terima'   => date('Y-m-d H:i:s', strtotime($create->received_at)),
                'diedit_oleh'      => $create->editBy->username,
                'dibuat_oleh'      => $create->createdBy->username,
                'diupdate_oleh'    => $create->updatedBy->username,
                'divalidasi_oleh'  => $create->validatedBy->username,
                'tanggal_validasi' => date('Y-m-d H:i:s', strtotime($create->validated_at))
            ])
            ->log('Menambah data edisi koleksi');

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

        Storage::disk($this->location->location)->delete($link);
        Collection::find($request->id)->update(['deposit' => null]);
        Collection::find($request->id)->delete();
        $media->delete();

        $data = Collection::withTrashed()->find($request->id);
        activity('collections')
            ->performedOn($data)
            ->causedBy(session('id'))
            ->withProperties([
                'edisi' => $data->edisi
            ])
            ->log('Menghapus data edisi koleksi');

        return response()->json(200);
    }
}
