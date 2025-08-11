<?php

use Illuminate\Database\Seeder;

class CollectionMediaSeeder extends Seeder
{

    public function run()
    {
        $collectionFiles = DB::connection('backup')
            ->table('coll_files')
            ->get();

        foreach ($collectionFiles as $collectionFile) {
            $collection = App\Models\Collection::where('id_old', $collectionFile->collection_id_coll)->first();

            if ($collection) {
                $method = 4; // Manual is_manual == 1
                if ($collection->is_manual == 0) {
                    $method = 3; //mandiri
                }
                $id = $collection->id;
                switch ($collection->type) {
                    case 1:
                        DB::connection('mysql')
                            ->table('collection_media')
                            ->insert([
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_cover_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '1', //Cover
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ],
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '2', //Original
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ],
                            ]);
                        break;
                    case 2:
                        DB::connection('mysql')
                            ->table('collection_media')
                            ->insert([
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_cover_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '1', //Cover
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ],
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '2', //Original
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ],
                            ]);
                        break;
                    case 4:
                        DB::connection('mysql')
                            ->table('collection_media')
                            ->insert([
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_cover_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '1', //Cover
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ],
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '2', //Original
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ]
                            ]);
                        break;
                    case 5:
                        DB::connection('mysql')
                            ->table('collection_media')
                            ->insert([
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_cover_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '1', //Cover
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ],
                                [
                                    'collection_id' => $id,
                                    'link'          => $collectionFile->file_url,
                                    'size'          => $collectionFile->size,
                                    'extension'     => $collectionFile->ext,
                                    'mimes'         => $collectionFile->mime,
                                    'hash'          => $collectionFile->hash,
                                    'type'          => '4', //Original
                                    'method'        => $method,
                                    'created_at'    => $collectionFile->created_at,
                                    'updated_at'    => $collectionFile->updated_at
                                ]
                            ]);
                        break;
                }
            }
        }
    }
}
