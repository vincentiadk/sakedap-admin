<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\User;
use App\Models\Publisher;
use App\Models\Contributor;
use App\Models\Collection as Book;
use App\Models\Author;
use App\Models\Location;
use App\Models\Setting;
use App\Models\CollectionContributor;
use App\Models\CollectionMedia;
use Illuminate\Support\Str;
use App\Helper\GeneralHelper;
use File;
use Storage;
use Mail;
use Log;
use App\Jobs\SendMailCollectionSubmitted;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FirstSheetBookImport implements ToCollection, WithHeadingRow
{
    private $folderName;
    private $userId;
    private $sessionName;
    private $sessionNameFailed;
    private $typeOfCollection;
    protected $location;

    function __construct($folderName, $userId, $sessionName, $sessionNameFailed, $typeOfCollection)
    {
        $this->folderName = $folderName;
        $this->userId = $userId;
        $this->sessionName = $sessionName;
        $this->sessionNameFailed = $sessionNameFailed;
        $this->typeOfCollection = $typeOfCollection;
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
                if ($this->typeOfCollection == 'isbn') {
                    $this->createCollectionIsbn($item, $publisher);
                } else $this->createCollection($item, $publisher);
            }
        }
    }

    private function createCollection($item, $publisher)
    {

        if ($item['preview'] == "") {
            $item['preview'] = '1-10';
        }

        if ($item['hak_akses'] == "") {
            $item['hak_akses'] = 2;
        }

        $collection = Book::create([
            'publisher_id'     => $publisher->id,
            'city_id'          => $publisher->city_id,
            'title'            => $item['judul'],
            'slug'             => Str::slug($item['judul'], '-'),
            'type'             => 1,
            'code_type'        => 1,
            'type_book'        => 1,
            'publication_year' => $item['tahun_terbit'],
            'description'      => $item['deskripsi'],
            'preview'          => $item['preview'],
            'access'           => $item['hak_akses'],
            'manual'           => 1,
            'deposit'          => GeneralHelper::depositCollection(),
            'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status'           => 1,
            'created_by'       => $this->userId,
            'updated_by'       => $this->userId,
            'validated_by'     => $this->userId,
            'validated_at'     => date('Y-m-d H:i:s')
        ]);

        session([
            "$this->sessionName" => session("$this->sessionName") . "<br/><br/>$collection->title"
        ]);

        $this->createAdditional($collection, $item);

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

    private function createCollectionIsbn($item, $publisher)
    {

        $code = $item['isbn'];
        $title = $item['judul'];

        if ($code == '') {
            session([
                "$this->sessionNameFailed" => session("$this->sessionNameFailed") . "<br/>$title tidak ada data ISBN"
            ]);
            return;
        }

        $explode = explode('~', $code);
        if (count($explode) > 1) {
            if (trim($explode[1]) != "") {
                session([
                    "$this->sessionNameFailed" => session("$this->sessionNameFailed") . "<br/>$code Merupakan ISBN Jilid, harus diunggah dengan unggah tunggal"
                ]);
                return;
            }
        }

        $publisher_code = $publisher->code_system;

        $dataIsbn = GeneralHelper::getDetailIsbn($request->isbn_book)[0];

        if (! str_contains($dataIsbn["Judul"], "elektronis]")) {
            session([
                "$this->sessionNameFailed" => session("$this->sessionNameFailed") . "<br/>$code Data ISBN bukan ISBN Elektronik"
            ]);
            return;
        }

        $type_book = 1;

        $code_kdt  = "";

        if ($code != '') {
            $book = Book::where('type', 1)
                ->where('code', $code)
                ->where('publisher_id', $publisher->id)
                ->first();

            if ($book) {
                session([
                    "$this->sessionNameFailed" => session("$this->sessionNameFailed") . "<br/>$code Data ISBN sudah pernah diunggah ke edeposit"
                ]);
                return;
            }
        }

        if ($item['preview'] == "") {
            $item['preview'] = '1-10';
        }

        if ($item['hak_akses'] == "") {
            $item['hak_akses'] = 2;
        }

        $publisherOnGroup = Publisher::checkGroupPublisher($publisher, $dataIsbn[0]["Penerbit"]);


        $collection = Book::create([
            'publisher_id'     => $publisherOnGroup->id,
            'title'            => $dataIsbn["Judul"],
            'slug'             => Str::slug($dataIsbn["Judul"], '-'),
            'type'             => 1,
            'type_book'        => $type_book,
            'kepeng'           => $dataIsbn["Pengarang"],
            'ddc'              => "",
            'edition'          => "",
            'series'           => "",
            'code'             => $code,
            'code_type'        => 1,
            'code_kdt'         => $code_kdt,
            'publication_year' => $dataIsbn["Tahun"],
            'description'      => $item['deskripsi'],
            'preview'          => $item['preview'],
            'access'           => $item['hak_akses'],
            'city_id'          => $publisherOnGroup->city_id,
            'manual'           => 1,
            'deposit'          => GeneralHelper::depositCollection(),
            'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $publisherOnGroup->name,
            'status'           => 1,
            'created_by'       => $this->userId,
            'updated_by'       => $this->userId
        ]);

        session([
            "$this->sessionName" => session("$this->sessionName") . "<br/><br/>$collection->title - $collection->code"
        ]);

        $this->createAdditional($collection, $item);

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

    private function createAdditional($collection, $item)
    {
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

        $coverName =  $fileName . '.jpg';
        $originalName =  $fileName . '.pdf';

        $coverUploadName =  date('Ymdhis') . '_' . $fileName . '.jpg';
        $originalUploadName =  date('Ymdhis') . '_' . $fileName . '.pdf';

        $coverPath = Storage::disk($this->location->location)->path('public/tmp/' . $this->folderName . '/' . $coverName);
        $originalPath = Storage::disk($this->location->location)->path('public/tmp/' . $this->folderName . '/' . $originalName);

        $coverUpload = Storage::disk($this->location->location)->path('public/collection/book/cover/' . $collection->id . '/' . $coverUploadName);
        $originalUpload = Storage::disk($this->location->location)->path('public/collection/book/original/' . $collection->id . '/' . $originalUploadName);

        if (!file_exists(Storage::disk($this->location->location)->path('public/collection/book/cover/' . $collection->id))) {
            mkdir(Storage::disk($this->location->location)->path('public/collection/book/cover/' . $collection->id), 0777, true);
        }

        if (!file_exists(Storage::disk($this->location->location)->path('public/collection/book/original/' . $collection->id))) {
            mkdir(Storage::disk($this->location->location)->path('public/collection/book/original/' . $collection->id), 0777, true);
        }

        File::copy($coverPath, $coverUpload);
        File::copy($originalPath, $originalUpload);

        $hash = md5_file($originalPath);

        CollectionMedia::insert([
            [
                'collection_id' => $collection->id,
                'link'          => 'public/collection/book/cover/' . $collection->id . '/' . $coverUploadName,
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
                'link'          => 'public/collection/book/original/' . $collection->id . '/' . $originalUploadName,
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

        GeneralHelper::pdfToImage('book', 'public/collection/book/original/' . $collection->id . '/' . $originalUploadName, $collection->id);

        session([
            "$this->sessionName" => session("$this->sessionName") . "<br/> hash: $hash"
        ]);

        unlink($coverPath);
        unlink($originalPath);
    }
}
