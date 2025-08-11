<?php

namespace App\Imports;

use App\Models\Bulk;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\BulkDetail;
use App\Models\Collection;
use App\Models\Location;
use Illuminate\Support\Str;
use App\Jobs\WatermarkAudio;
use App\Helper\GeneralHelper;
use App\Jobs\PDFToImage;
use App\Models\CollectionMedia;
use Illuminate\Validation\Rule;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection as ImportCollection;

class CollectionImport implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow
{

    use Importable;

    protected $request;
    protected $location;

    public function __construct($request)
    {
        $this->request = $request;
        $this->location = Location::where('active', 1)->first();
    }

    public function collection(ImportCollection $rows)
    {

        // dd($rows);
        $directory = Str::before($this->request->file_excel, 'data.xlsx');
        if ($this->request->flag == 'serial') {
            Validator::make($rows->toArray(), [
                'edisi_volume'   => ['required'],
                'tanggal_terbit' => ['required'],
                'nama_file'      => ['required']
            ]);
        } else {
            Validator::make($rows->toArray(), [
                'judul'        => ['required'],
                'kontributor'  => ['required'],
                'tahun_terbit' => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
                'preview'      => ['required'],
                'hak_akses'    => ['required', Rule::in(['1', '2', '3', '4'])],
                'nama_file'    => ['required']
            ]);
        }

        foreach ($rows as $r) {
            if ($this->request->flag == 'serial') {
                $title      = $r['edisi_volume'];
                $data       = Collection::find($this->request->collection_id);
                $collection = Collection::create([
                    'parent_id'        => $this->request->collection_id,
                    'publisher_id'     => $data->publisher_id,
                    'type'             => 4,
                    'deposit_head_id'  => 4,
                    'edition'          => $r['edisi_volume'],
                    'deposit'          => GeneralHelper::depositCollection(),
                    'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $data->publisher->name,
                    'date'             => $r['tanggal_terbit'],
                    'status'           => 2,
                    'received_at'      => date('Y-m-d H:i:s'),
                    'edit_by'          => $this->request->user_id,
                    'created_by'       => $this->request->user_id,
                    'updated_by'       => $this->request->user_id,
                    'validated_by'     => $this->request->user_id,
                    'received_by'      => $this->request->user_id,
                ]);
            } else {
                $title       = $r['judul'];
                $publisher   = Publisher::find($this->request->publisher_id);
                $contributor = explode(';', $r['kontributor']);

                $collection = Collection::create([
                    'publisher_id'     => $this->request->publisher_id,
                    'city_id'          => $publisher->city_id,
                    'title'            => $r['judul'],
                    'type'             => $this->request->type,
                    'deposit_head_id'  => $this->request->type,
                    'deposit'          => GeneralHelper::depositCollection(),
                    'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                    'code'             => $r['code'],
                    'publication_year' => $r['tahun_terbit'],
                    'preview'          => $r['preview'],
                    'description'      => $r['deskripsi'],
                    'access'           => $r['hak_akses'],
                    'status'           => 2,
                    'received_at'      => date('Y-m-d H:i:s'),
                    'edit_by'          => $this->request->user_id,
                    'created_by'       => $this->request->user_id,
                    'updated_by'       => $this->request->user_id,
                    'received_by'      => $this->request->user_id,
                ]);

                foreach ($contributor as $c) {
                    $author_check = Author::updateOrCreate([
                        'slug' => Str::slug($c, '-')
                    ], [
                        'fullname' => $c
                    ]);

                    $author = Author::where('fullname', $c)
                        ->where('slug', Str::slug($c, '-'))
                        ->first();

                    CollectionContributor::create([
                        'collection_id'  => $collection->id,
                        'contributor_id' => 185,
                        'author_id'      => $author->id
                    ]);
                }
            }

            if ($collection) {
                if ($collection->type == 1) {
                    $type = 'book';
                } else if ($collection->type == 2) {
                    $type = 'partitur';
                } else if ($collection->type == 3) {
                    $type = 'map';
                } else if ($collection->type == 4) {
                    $type = 'edition';
                } else if ($collection->type == 5) {
                    $type = 'audio';
                } else if ($collection->type == 6) {
                    $type = 'film';
                } else {
                    $type = 'invalid';
                }

                $filename_cover = Str::random(40) . '.jpg';
                $link_cover     = 'public/collection/' . $type . '/' . $collection->id . '/' . $filename_cover;
                $path_cover     = Storage::disk($this->location->location)->path($link_cover);
                Storage::move($directory . $r['nama_file'] . '.jpg', $link_cover);

                CollectionMedia::create([
                    'collection_id' => $collection->id,
                    'link'          => $link_cover,
                    'size'          => File::size($path_cover),
                    'extension'     => 'jpg',
                    'mimes'         => 'image/jpg',
                    'hash'          => md5_file($path_cover),
                    'type'          => 1,
                    'method'        => 7,
                    'location_id'   => $this->location->id
                ]);

                if ($this->request->type == 5) {
                    $extension = 'wav';
                } else if ($this->request->type == 6) {
                    $extension = 'mp4';
                } else {
                    $extension = 'pdf';
                }

                Storage::disk($this->location->location)->makeDirectory('public/collection/' . $type . '/preview/' . $collection->id);
                Storage::disk($this->location->location)->makeDirectory('public/collection/' . $type . '/watermark/' . $collection->id);

                if ($this->request->flag == 'serial') {
                    $prev_start = 1;
                    $prev_end   = 1;
                } else {
                    $preview    = explode('-', $r['preview']);
                    $prev_start = $preview[0];
                    $prev_end   = $preview[1];
                }

                $filename_original = Str::random(40) . '.' . $extension;
                $link_original     = 'public/collection/' . $type . '/original/' . $collection->id . '/' . $filename_original;
                $dir_original      = Storage::disk($this->location->location)->path($link_original);
                Storage::move($directory . $r['nama_file'] . '.' . $extension, $link_original);

                $filename_preview = Str::random(40) . '.' . $extension;
                $link_preview     = 'public/collection/' . $type . '/preview/' . $collection->id . '/' . $filename_preview;
                $dir_preview      = Storage::disk($this->location->location)->path($link_preview);

                $filename_watermark = Str::random(40) . '.' . $extension;
                $link_watermark     = 'public/collection/' . $type . '/watermark/' . $collection->id . '/' . $filename_watermark;
                $dir_watermark      = Storage::disk($this->location->location)->path($link_watermark);

                if ($this->request->type == 5) {
                    $create_media = CollectionMedia::create([
                        'collection_id' => $collection->id,
                        'link'          => $link_original,
                        'size'          => File::size($dir_original),
                        'extension'     => 'wav',
                        'mimes'         => 'audio/x-wav',
                        'hash'          => md5_file($dir_original),
                        'type'          => 5,
                        'method'        => 7,
                        'location_id'   => $this->location->id
                    ]);

                    dispatch(new WatermarkAudio($dir_original, $create_media))->onQueue('audio');
                    CollectionMedia::insert([
                        [
                            'collection_id' => $collection->id,
                            'link'          => $link_preview,
                            'size'          => File::size($dir_preview),
                            'extension'     => $extension,
                            'mimes'         => 'audio/x-wav',
                            'hash'          => md5_file($dir_preview),
                            'type'          => 5,
                            'method'        => 7,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ],
                        [
                            'collection_id' => $collection->id,
                            'link'          => $link_watermark,
                            'size'          => File::size($dir_watermark),
                            'extension'     => $extension,
                            'mimes'         => 'audio/x-wav',
                            'hash'          => md5_file($dir_watermark),
                            'type'          => 6,
                            'method'        => 7,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ]
                    ]);

                    BulkDetail::create([
                        'bulk_id'     => $this->request->bulk_id,
                        'title'       => $title,
                        'description' => 'Berhasil ditambahkan',
                        'status'      => 1
                    ]);
                } else if ($this->request->type == 6) {
                    GeneralHelper::videoCut($dir_original, $link_preview, $prev_start, $prev_end);
                    GeneralHelper::videoWatermark($dir_original, $link_watermark);

                    CollectionMedia::insert([
                        [
                            'collection_id' => $collection->id,
                            'link'          => $link_original,
                            'size'          => File::size($dir_original),
                            'extension'     => $extension,
                            'mimes'         => 'video/mp4',
                            'hash'          => md5_file($dir_original),
                            'type'          => 7,
                            'method'        => 7,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ],
                        [
                            'collection_id' => $collection->id,
                            'link'          => $link_preview,
                            'size'          => File::size($dir_preview),
                            'extension'     => $extension,
                            'mimes'         => 'video/mp4',
                            'hash'          => md5_file($dir_preview),
                            'type'          => 8,
                            'method'        => 7,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ],
                        [
                            'collection_id' => $create->id,
                            'link'          => $link_watermark,
                            'size'          => File::size($dir_watermark),
                            'extension'     => $extension,
                            'mimes'         => 'video/mp4',
                            'hash'          => md5_file($dir_watermark),
                            'type'          => 9,
                            'method'        => 7,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ]
                    ]);

                    BulkDetail::create([
                        'bulk_id'     => $this->request->bulk_id,
                        'title'       => $title,
                        'description' => 'Berhasil ditambahkan',
                        'status'      => 1
                    ]);
                } else {
                    $job = new PDFToImage($type, $link_original, $collection->id);
                    dispatch(($job)->onQueue('convert_pdf'));
                    BulkDetail::create([
                        'bulk_id'     => $this->request->bulk_id,
                        'title'       => $title,
                        'description' => 'Berhasil ditambahkan',
                        'status'      => 1
                    ]);
                }
            } else {
                BulkDetail::create([
                    'bulk_id'     => $this->request->bulk_id,
                    'title'       => $title,
                    'description' => 'Gagal waktu menambah data',
                    'status'      => 2
                ]);
            }
        }

        Storage::deleteDirectory(Str::before($this->request->file_excel, '/data.xlsx'));
        Bulk::find($this->request->bulk_id)->update([
            'process_finish_at' => date('Y-m-d H:i:s'),
            'status'            => 1
        ]);
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
