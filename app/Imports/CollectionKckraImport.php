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
use App\Models\CollectionCopy;
use App\Models\LibraryLocation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Collection as ImportCollection;

class CollectionKckraImport implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithCalculatedFormulas
{

    use Importable;

    protected $request;
    protected $location;

    public function __construct($request)
    {
        $this->request = (object) $request;
        $this->location = Location::where('active', 1)->first();
    }

    public function collection(ImportCollection $rows)
    {
        try {
            $data = [];
            $lib_loc = LibraryLocation::where('library_id', $this->request->library_id)->where('publish', '1')->pluck('id', 'name')->toArray();
            $condition = ['Sangat Baik' => '1', 'Baik' => '2', 'Cukup' => '3', 'Rusak' => '4'];
            $directory = Str::before($this->request->file_excel, 'data.xlsx');
            if ($this->request->flag == 'serial') {
                foreach ($rows->toArray(null, true) as $key => $value) {
                    if (isset($value['edisi_volume']) && !empty($value['edisi_volume'])) {
                        if (!empty($value['tanggal_publikasi_awal']) && !isset($data[$value['edisi_volume']])) {
                            $data[$value['edisi_volume']] = $value;
                            unset($data[$value['edisi_volume']]['eksemplar']);
                            unset($data[$value['edisi_volume']]['lokasi']);
                            unset($data[$value['edisi_volume']]['kondisi']);
                            // unset($data[$value['edisi_volume']]['tanggal_terima']);
                        }

                        if (!empty($value['eksemplar']) && !empty($value['lokasi'])) {
                            for ($i = 0; $i < $value['eksemplar']; $i++) {
                                $data[$value['edisi_volume']]['copy'][] = [
                                    'lib_loc_id' => $lib_loc[$value['lokasi']],
                                    'condition' => $condition[$value['kondisi']],
                                    'received_at' => empty($value['tanggal_terima']) ? date('Y-m-d') : $value['tanggal_terima']
                                ];
                            }
                        }
                    }
                }

                // dd($data);
                $validator = Validator::make($data, [
                    '*.edisi_volume'            => ['required'],
                    '*.tanggal_publikasi_awal'  => ['required'],
                    '*.tanggal_publikasi_akhir' => ['required'],
                    '*.copy'                    => ['required'],
                    '*.nama_file'               => ['required']
                ], [
                    '*.copy.required'        => 'Harap pastikan kolom eksemplar dan lokasi sudah terisi!',
                ]);
            } else {
                foreach ($rows->toArray(null, true) as $key => $value) {
                    if (isset($value['judul']) && !empty($value['judul'])) {
                        if (!empty($value['kontributor']) && !isset($data[$value['judul']])) {
                            $data[$value['judul']] = $value;
                            unset($data[$value['judul']]['eksemplar']);
                            unset($data[$value['judul']]['lokasi']);
                            unset($data[$value['judul']]['kondisi']);
                            // unset($data[$value['judul']]['tanggal_terima']);
                        }

                        if (!empty($value['eksemplar']) && !empty($value['lokasi'])) {
                            for ($i = 0; $i < $value['eksemplar']; $i++) {
                                $data[$value['judul']]['copy'][] = [
                                    'lib_loc_id' => $lib_loc[$value['lokasi']],
                                    'condition' => $condition[$value['kondisi']],
                                    'received_at' => empty($value['tanggal_terima']) ? date('Y-m-d') : $value['tanggal_terima']
                                ];
                            }
                        }
                    }
                }

                $validator = Validator::make($data, [
                    '*.judul'        => ['required'],
                    '*.code'         => ['nullable', 'sometimes', 'unique:collections,code', 'distinct'],
                    '*.kontributor'  => ['required'],
                    '*.tahun_terbit' => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
                    '*.copy'         => ['required'],
                    '*.nama_file'    => ['required']
                ], [
                    '*.copy.required' => 'Harap pastikan kolom eksemplar dan lokasi sudah terisi!',
                    '*.code.unique'   => 'Kode Unik (ex: ISBN, ISSN, dll) sudah ada pada sistem, mohon pastikan tidak ada duplikasi!',
                    '*.code.distinct' => 'Ada duplikat dalam Kode Unik (ex: ISBN, ISSN, dll) pada file upload!',
                ]);
            }

            if ($validator->fails()) {
                BulkDetail::create([
                    'bulk_id'     => $this->request->bulk_id,
                    'title'       => '-',
                    'description' => $validator->errors(),
                    'status'      => 3
                ]);

                Bulk::find($this->request->bulk_id)->update([
                    'process_finish_at' => date('Y-m-d H:i:s'),
                    'status'            => 3
                ]);
            } else {

                foreach ($data as $r) {
                    if ($this->request->flag == 'serial') {
                        $title      = $r['edisi_volume'];
                        $parent      = Collection::find($this->request->collection_id);
                        $collection = Collection::create([
                            'parent_id'        => $this->request->collection_id,
                            'publisher_id'     => $parent->publisher_id,
                            'type'             =>  $this->request->type,
                            'deposit_head_id'  =>  $this->request->type,
                            'edition'          => $r['edisi_volume'],
                            'start_publication_date' => date('Y-m-d', strtotime($r['tanggal_publikasi_awal'])),
                            'end_publication_date' => date('Y-m-d', strtotime($r['tanggal_publikasi_akhir'])),
                            'deposit'          => GeneralHelper::depositCollection(),
                            'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $parent->publisher->name,
                            'date'             => date('Y-m-d', strtotime($r['tanggal_publikasi_awal'])),
                            'currency'         => $r['kurs'],
                            'price'            => $r['harga'],
                            'status'           => 2,
                            'received_at'      => !empty($r['tanggal_terima']) ? $r['tanggal_terima'] : date('Y-m-d'),
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
                            'title_ori'        => $r['judul'],
                            'type'             => $this->request->type,
                            'deposit_head_id'  => $this->request->type,
                            'deposit'          => GeneralHelper::depositCollection(),
                            'copyright'        => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'code'             => $r['code'],
                            'publication_year' => $r['tahun_terbit'],
                            'description'      => $r['deskripsi'],
                            'currency'         => $r['kurs'],
                            'price'            => $r['harga'],
                            'status'           => 2,
                            'received_at'      => !empty($r['tanggal_terima']) ? $r['tanggal_terima'] : date('Y-m-d'),
                            'slug'             => Str::slug($r['judul'], '-'),
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

                        if (isset($r['copy'])) {
                            foreach ($r['copy'] as $copy) {
                                CollectionCopy::create([
                                    'received_at' => $copy['received_at'],
                                    'received_by' => $this->request->user_id,
                                    'availability' => '6',
                                    'collection_id' => $collection->id,
                                    'lib_loc_id' => $copy['lib_loc_id'],
                                    'condition' => $copy['condition'],
                                    'created_by' => $this->request->user_id,
                                    'edit_by' => $this->request->user_id
                                ]);
                            }
                        }

                        $name_cover     = $collection->depositHead->code;
                        $filename_cover = Str::random(40) . '.jpg';
                        $link_cover     = 'public/collection/' . $name_cover . '/cover/' . $collection->id . '/' . $filename_cover;
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

                        BulkDetail::create([
                            'bulk_id'     => $this->request->bulk_id,
                            'title'       => $title,
                            'description' => 'Success',
                            'status'      => 1
                        ]);
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

            // dd($rows->toArray(null, true));

        } catch (\Exception $e) {
            BulkDetail::create([
                'bulk_id'     => $this->request->bulk_id,
                'title'       => '',
                'description' => $e->getMessage(),
                'status'      => 3
            ]);

            Bulk::find($this->request->bulk_id)->update([
                'process_finish_at' => date('Y-m-d H:i:s'),
                'status'            => 3
            ]);
        }
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
