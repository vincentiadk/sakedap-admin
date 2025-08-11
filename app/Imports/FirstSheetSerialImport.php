<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\User;
use App\Models\Publisher;
use App\Models\Contributor;
use App\Models\Collection as Serial;
use App\Models\Author;
use App\Models\Location;
use App\Models\Setting;
use App\Models\CollectionContributor;
use App\Models\CollectionMedia;
use Illuminate\Support\Str;
use App\Helper\GeneralHelper;
use File;
use Mail;
use Storage;
use App\Jobs\SendMailCollectionSubmitted;

class FirstSheetSerialImport implements ToCollection, WithHeadingRow
{
    private $folderName;
    private $userId;
    private $collectionId;
    private $sessionName;
    protected $location;

    function __construct($folderName, $userId, $collectionId, $sessionName)
    {
        $this->folderName = $folderName;
        $this->userId = $userId;
        $this->collectionId = $collectionId;
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

        $collection = Serial::create([
            'parent_id'         => $this->collectionId,
            'publisher_id'      => $publisher->id,
            'type'              => 4,
            'edition'           => $item['edisi'],
            'date'              => date('Y-m-d', strtotime($item['tanggal'])),
            'manual'            => 1,
            'deposit'           => GeneralHelper::depositCollection(),
            'copyright'         => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status'            => 1,
            'created_by'        => $this->userId,
            'updated_by'        => $this->userId
        ]);


        $fileName = substr($item['nama_file'], 0, (strrpos($item['nama_file'], ".")));

        $coverName = $fileName . '.jpg';
        $originalName = $fileName . '.pdf';

        $coverUploadName =  date('Ymdhis') . '_' . $fileName . '.jpg';
        $originalUploadName =  date('Ymdhis') . '_' . $fileName . '.pdf';

        $coverPath = Storage::disk($this->location->location)->path('public/tmp/' . $this->folderName . '/' . $coverName);
        $originalPath = Storage::disk($this->location->location)->path('public/tmp/' . $this->folderName . '/' . $originalName);

        $coverUpload = Storage::disk($this->location->location)->path('public/collection/serial/cover/' . $collection->id . '/'  . $coverUploadName);
        $originalUpload = Storage::disk($this->location->location)->path('public/collection/serial/original/' . $collection->id . '/' . $originalUploadName);

        if (!file_exists(Storage::disk($this->location->location)->path('public/collection/serial/cover/' . $collection->id))) {
            mkdir(Storage::disk($this->location->location)->path('public/collection/serial/cover/' . $collection->id), 0777, true);
        }

        if (!file_exists(Storage::disk($this->location->location)->path('public/collection/serial/original/' . $collection->id))) {
            mkdir(Storage::disk($this->location->location)->path('public/collection/serial/original/' . $collection->id), 0777, true);
        }

        File::copy($coverPath, $coverUpload);
        File::copy($originalPath, $originalUpload);

        $hash = md5_file($originalPath);

        CollectionMedia::insert([
            [
                'collection_id' => $collection->id,
                'link'          => 'public/collection/serial/cover/' . $collection->id . '/' . $coverUploadName,
                'size'          => File::size($coverPath),
                'extension'     => File::extension($coverPath),
                'mimes'         => File::mimeType($coverPath),
                'hash'          => md5_file($coverPath),
                'type'          => 1,
                'method'        => 6,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'location_id'   => $this->location->id
            ],
            [
                'collection_id' => $collection->id,
                'link'          => 'public/collection/serial/original/' . $collection->id . '/' . $originalUploadName,
                'size'          => File::size($originalPath),
                'extension'     => File::extension($originalPath),
                'mimes'         => File::mimeType($originalPath),
                'hash'          => $hash,
                'type'          => 2,
                'method'        => 6,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'location_id'   => $this->location->id
            ]
        ]);

        GeneralHelper::pdfToImage('serial', 'public/collection/serial/original/' . $collection->id . '/' . $originalUploadName, $collection->id);

        session([
            "$this->sessionName" => session("$this->sessionName") . "<br/> hash: $hash"
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
