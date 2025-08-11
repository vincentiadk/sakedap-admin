<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\User;
use App\Models\Publisher;
use App\Models\Contributor;
use App\Models\Collection as Film;
use App\Models\Author;
use App\Models\Location;
use App\Models\Setting;
use App\Models\CollectionContributor;
use App\Models\CollectionMedia;
use Illuminate\Support\Str;
use App\Helper\GeneralHelper;
use Mail;
use File;
use Storage;
use App\Jobs\SendMailCollectionSubmitted;

class FirstSheetFilmImport implements ToCollection, WithHeadingRow
{
    private $folderName;
    private $userId;
    private $sessionName;
    protected $location;

    function __construct($folderName, $userId, $sessionName)
    {
        $this->folderName = $folderName;
        $this->userId = $userId;
        $this->sessionName = $sessionName;
        $this->location = Location::where('active', 1)->first();
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $user = User::find($this->userId);
        $publisher = Publisher::find($user->userable_id);

        foreach ($collection as $item) {
            if ($item['nama_file'] != "") {
                $this->createCollection($item, $publisher);
            }
        }
    }

    private function createCollection($item, $publisher)
    {

        if ($item['isan'] != '') {
            $code      = $item['isan'];
            $code_type = 5;
        } else {
            $code      = null;
            $code_type = null;
        }

        if ($item['isan'] != '') {
            $partitur = Film::where('type', 6)
                ->where('code', $item['isan'])
                ->where('publisher_id', $publisher->id)
                ->first();

            if ($partitur) {
                return;
            }
        }

        $collection = Film::create([
            'publisher_id'     => $publisher->id,
            'city_id'          => $publisher->city_id,
            'title'            => $item['judul'],
            'slug'             => Str::slug($item['judul'], '-'),
            'type'             => 6,
            'code'             => $code,
            'code_type'        => $code_type,
            'publication_year' => $item['tahun_terbit'],
            'description'      => $item['deskripsi'],
            'preview'          => $item['preview'],
            'access'           => $item['hak_akses'],
            'manual'           => 1,
            'deposit'          => GeneralHelper::depositCollection(),
            'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status'           => 1,
            'created_by'       => $this->userId,
            'updated_by'       => $this->userId
        ]);

        $subsAuthor = explode(';', $item['penulis_dan_kontributor_lainnya']);

        foreach ($subsAuthor as $key => $name) {

            $author = Author::updateOrCreate([
                'slug'          => Str::slug($name, '-')
            ], [
                'fullname'      => $name,
                'slug'          => Str::slug($name, '-'),
            ]);

            $contributor = Contributor::updateOrCreate([
                'slug'          => Str::slug('penulis', '-'),
                'type'          => 1
            ], [
                'name'          => 'penulis',
                'slug'          => Str::slug('penulis', '-'),
                'type'          => 1
            ]);

            CollectionContributor::create([
                'collection_id'  => $collection->id,
                'contributor_id' => $contributor->id,
                'author_id'      => $author->id
            ]);
        }


        $fileName = substr($item['nama_file'], 0, (strrpos($item['nama_file'], ".")));

        $coverName = $fileName . '.jpg';
        $originalName = $fileName . '.mp4';

        $coverUploadName =  date('Ymdhis') . '_' . $fileName . '.jpg';
        $originalUploadName =  date('Ymdhis') . '_' . $fileName . '.mp4';

        $coverPath = Storage::disk($this->location->location)->path('public/tmp/' . $this->folderName . '/' . $coverName);
        $originalPath = Storage::disk($this->location->location)->path('public/tmp/' . $this->folderName . '/' . $originalName);

        $coverUpload = Storage::disk($this->location->location)->path('public/collection/video/cover/' . $collection->id . '/' . $coverUploadName);
        $originalUpload = Storage::disk($this->location->location)->path('public/collection/video/original/' . $collection->id . '/' . $originalUploadName);


        if (!file_exists(Storage::disk($this->location->location)->path('public/collection/video/cover/' . $collection->id))) {
            mkdir(Storage::disk($this->location->location)->path('public/collection/video/cover/' . $collection->id), 0777, true);
        }

        if (!file_exists(Storage::disk($this->location->location)->path('public/collection/video/original/' . $collection->id))) {
            mkdir(Storage::disk($this->location->location)->path('public/collection/video/original/' . $collection->id), 0777, true);
        }

        File::copy($coverPath, $coverUpload);
        File::copy($originalPath, $originalUpload);

        $hash = md5_file($originalPath);

        CollectionMedia::insert([
            [
                'collection_id' => $collection->id,
                'link'          => 'public/collection/video/cover/' . $coverUploadName,
                'size'          => File::size($coverPath),
                'extension'     => File::extension($coverPath),
                'mimes'         => File::mimeType($coverPath),
                'hash'          => md5_file($coverPath),
                'type'          => 'Cover',
                'method'        => 6,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'location_id'   => $this->location->id
            ],
            [
                'collection_id' => $collection->id,
                'link'          => 'public/collection/video/original/' . $originalUploadName,
                'size'          => File::size($originalPath),
                'extension'     => File::extension($originalPath),
                'mimes'         => File::mimeType($originalPath),
                'hash'          => $hash,
                'type'          => 'Original',
                'method'        => 6,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'location_id'   => $this->location->id
            ]
        ]);

        session([
            "$this->sessionName" => session("$this->sessionName") . "<br/><br/>$collection->title - $collection->code"
        ]);

        unlink($coverPath);
        unlink($originalPath);

        activity('collections')
            ->performedOn($collection)
            ->causedBy($this->userId)
            ->withProperties([
                'penerbit'         => $collection->publisher->name,
                'judul'            => $collection->title,
                'deskripsi_fisik'  => $collection->physical_description,
                'tipe'             => $collection->type(),
                'album'            => $collection->album,
                'tipe_buku'        => $collection->typeBook(),
                'edisi'            => $collection->edition,
                'kode'             => $collection->code,
                'tipe_kode'        => $collection->codeType(),
                'kode_kdt'         => $collection->code_kdt,
                'bulan_terbit'     => $collection->publication_month,
                'tahun_terbit'     => $collection->publication_year,
                'seri'             => $collection->series,
                'serial'           => $collection->serial,
                'ddc'              => $collection->ddc,
                'volume'           => $collection->volume,
                'preview'          => $collection->preview,
                'description'      => $collection->description,
                'deposit'          => $collection->deposit,
                'copyright'        => $collection->copyright,
                'akses'            => $collection->access,
                'status'           => $collection->status(),
                'dibuat_oleh'      => $collection->createdBy->username,
            ])
            ->log('Menambah data koleksi dari bulk upload.');
    }
}
