<?php

use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{

    public function run()
    {
        $collections = DB::connection('backup')
            ->table('collections')
            ->get();

        foreach ($collections as $collection) {
            $created_by = 1;
            $updated_by = 1;
            $validated_by = 1;
            $edit_by = null;
            if ($collection->created_by != "") {
                $created_by =  App\Models\User::where('username', $collection->created_by)->first() ? App\Models\User::where('username', $collection->created_by)->first()->id : 1;
            } else {
                $create_publisher = DB::connection('backup')->table('publishers')->where('id', $collection->publisher_id)->first();
                $created_by = $create_publisher->user_id;
            }
            if ($collection->updated_by != "") {
                $updated_by = App\Models\User::where('username', $collection->updated_by)->first() ? App\Models\User::where('username', $collection->updated_by)->first()->id : 1;
            }
            if ($collection->validated_by != "") {
                $validated_by = App\Models\User::where('username', $collection->validated_by)->first() ? App\Models\User::where('username', $collection->validated_by)->first()->id : 1;
            }
            if ($collection->edit_by != "") {
                $edit_by = App\Models\User::where('username', $collection->edit_by)->first() ? App\Models\User::where('username', $collection->edit_by)->first()->id : null;
            }

            $status = "1";
            if ($collection->validation == "Y") {
                $status = "2";
            } else if ($collection->validation == "P") {
                $status = "3";
            }
            $problems = DB::connection('backup')->table('coll_problem')->where('collection_id_coll', $collection->id_coll)->get();
            $problem_lainnya = "";
            foreach ($problems as $problem) {
                $problem_lainnya .= $problem->name_problem . ". ";
            }

            $publisher = DB::connection('backup')->table('publishers')->where('id', $collection->publisher_id)->first();
            $publisher_name = $publisher->name;
            $city = App\Models\City::where('name', 'LIKE', $collection->coverage)->first();
            if (!$city) {
                if ($publisher->city_id != "") {
                    $city = App\Models\City::find($publisher->city_id);
                }
            }
            $checkSlug  = App\Models\Collection::where('slug', \Str::slug($collection->title))->count();

            $date = 0;
            if (strlen(trim($collection->date)) == 4) {
                $date = $collection->date;
            } else if (strlen(trim($collection->date)) == 10) {
                $date = date('Y', strtotime(trim($collection->date)));
            }

            switch ($collection->id_type) {
                case "1":
                    $this->command->info('Book : ' . $collection->title);
                    $id = \App\Models\Collection::insertGetId([
                        'id_old'            => $collection->id_coll,
                        'publisher_id'      => $collection->publisher_id,
                        'city_id'           => $city ? $city->id : null,
                        'title'             => $collection->title,
                        'slug'              => $checkSlug  > 0 ? \Str::slug($collection->title) . '_' . $checkSlug : \Str::slug($collection->title),
                        'type'              => $collection->id_type,
                        'edition'           => null,
                        'serial'            => null,
                        'ddc'               => null,
                        'volume'            => null,
                        'deposit'           => $collection->id_deposit,
                        'code'              => $collection->identifier,
                        'code_type'         => 1, //ISBN
                        'code_kdt'          => $collection->kodepubdtl,
                        'source'            => null,
                        'publication_year'  => $date,
                        'received_at'       => $collection->validated_at != "" ? $collection->validated_at : null,
                        'copyright'         => "Copyright (c) " . $date . " " . $publisher_name,
                        'preview'           => "1-10",
                        'description'       => $collection->description,
                        'problem'           => $problem_lainnya,
                        'lock'              => $collection->is_lock,
                        'manual'            => $collection->is_manual,
                        'status'            => $status,
                        'created_by'        => $created_by,
                        'updated_by'        => $updated_by,
                        'edit_by'           => $edit_by,
                        'validated_by'      => $validated_by,
                        'validated_at'      => $collection->validated_at,
                        'created_at'        => $collection->created_at,
                        'updated_at'        => $collection->updated_at,
                        'received_at'       => $collection->created_at
                    ]);

                    $this->saveSubject($collection, $id);
                    $this->saveContributor($collection, $id);
                    $this->saveCreator($collection, $id);
                    break;

                case "2":
                    $this->command->info('Partitur : ' . $collection->title);
                    $id = \App\Models\Collection::insertGetId([
                        'id_old'            => $collection->id_coll,
                        'publisher_id'      => $collection->publisher_id,
                        'city_id'           => $city ? $city->id : null,
                        'title'             => $collection->title,
                        'slug'              => $checkSlug  > 0 ? \Str::slug($collection->title) . '_' . $checkSlug : \Str::slug($collection->title),
                        'type'              => $collection->id_type,
                        'edition'           => null,
                        'serial'            => null,
                        'ddc'               => null,
                        'volume'            => null,
                        'deposit'           => $collection->id_deposit,
                        'code'              => $collection->identifier,
                        'code_type'         => 2, //ISMN
                        'code_kdt'          => null,
                        'source'            => null,
                        'publication_year'  => $date,
                        'received_at'       => $collection->validated_at != "" ? $collection->validated_at : null,
                        'copyright'         => "Copyright (c) " . $date . " " . $publisher_name,
                        'preview'           => "1-1",
                        'description'       => $collection->description,
                        'problem'           => $problem_lainnya,
                        'lock'              => $collection->is_lock,
                        'manual'            => $collection->is_manual,
                        'status'            => $status,
                        'created_by'        => $created_by,
                        'updated_by'        => $updated_by,
                        'edit_by'           => $edit_by,
                        'validated_by'      => $validated_by,
                        'validated_at'      => $collection->validated_at,
                        'created_at'        => $collection->created_at,
                        'updated_at'        => $collection->updated_at,
                        'received_at'       => $collection->created_at
                    ]);
                    $this->saveSubject($collection, $id);
                    $this->saveContributor($collection, $id);
                    $this->saveCreator($collection, $id);
                    break;

                case "4":

                    $parent = DB::connection('backup')
                        ->table('collections')
                        ->where('id_type', 4)
                        ->where('title', $collection->title)
                        ->first();
                    $publisher_name = DB::connection('backup')->table('publishers')->where('id', $parent->publisher_id)->first()->name;
                    $serial = strtolower(explode(",", explode(";", $parent->relation)[0])[1]);
                    switch ($serial) {
                        case "daily":
                            $serial = 1;
                            break;
                        case "weekly":
                            $serial = 2;
                            break;
                        case "monthly":
                            $serial = 3;
                            break;
                        default:
                            break;
                    }
                    if (App\Models\Collection::where('slug', \Str::slug($collection->title))->count() < 1) {
                        $this->command->info('Serial Parent : ' . $collection->title);
                        $id = \App\Models\Collection::insertGetId([
                            'publisher_id'      => $parent->publisher_id,
                            'city_id'           => $city ? $city->id : null,
                            'parent_id'         => 0,
                            'title'             => $collection->title,
                            'slug'              => ($checkSlug  > 0) ? \Str::slug($collection->title) . '_' . $checkSlug : \Str::slug($collection->title),
                            'type'              => $collection->id_type,
                            'edition'           => null,
                            'album'             => null,
                            'serial'            => null,
                            'ddc'               => null,
                            'volume'            => null,
                            'deposit'           => $collection->validated_at == "" ? App\Helper\GeneralHelper::depositCollection($collection->created_at) :  App\Helper\GeneralHelper::depositCollection($collection->validated_at),
                            'code'              => $collection->identifier,
                            'code_type'         => 3, //ISSN
                            'code_kdt'          => null,
                            'source'            => null,
                            'publication_year'  => $date,
                            'received_at'       => $collection->validated_at != "" ? $collection->validated_at : null,
                            'copyright'         => "Copyright (c) " . $date . " " . $publisher_name,
                            'preview'           => "1-3",
                            'description'       => $collection->description,
                            'problem'           => $problem_lainnya,
                            'lock'              => $collection->is_lock,
                            'manual'            => $collection->is_manual,
                            'status'            => 2,
                            'created_by'        => $created_by,
                            'updated_by'        => $updated_by,
                            'edit_by'           => $edit_by,
                            'validated_by'      => $validated_by,
                            'validated_at'      => $collection->validated_at,
                            'created_at'        => $collection->created_at,
                            'updated_at'        => $collection->updated_at,
                            'received_at'       => $collection->validated_at == "" ? $collection->validated_at : $collection->created_at
                        ]);
                        $this->saveSubject($parent, $id);
                    }
                    $this->command->info('Serial Child : ' . $collection->title);
                    $volume = explode(";", $collection->relation)[1];
                    $edition = substr($volume, 7, strlen($volume));
                    $parent_id = App\Models\Collection::where('slug', \Str::slug($collection->title))->first()->id;
                    \App\Models\Collection::insert([
                        'id_old'            => $collection->id_coll,
                        'publisher_id'      => $parent->publisher_id,
                        'city_id'           => $city ? $city->id : null,
                        'parent_id'         => $parent_id,
                        'title'             => null,
                        'slug'              => null,
                        'type'              => $collection->id_type,
                        'edition'           => $edition,
                        'album'             => null,
                        'serial'            => $serial,
                        'ddc'               => null,
                        'volume'            => null,
                        'deposit'           => $collection->id_deposit,
                        'code'              => $collection->identifier,
                        'code_type'         => 3, //ISSN
                        'code_kdt'          => null,
                        'source'            => null,
                        'publication_year'  => $date,
                        'received_at'       => $collection->validated_at != "" ? $collection->validated_at : null,
                        'copyright'         => "Copyright (c) " . $date . " " . $publisher_name,
                        'preview'           => "1-3",
                        'description'       => null,
                        'problem'           => $problem_lainnya,
                        'lock'              => $collection->is_lock,
                        'manual'            => $collection->is_manual,
                        'status'            => $status,
                        'created_by'        => $created_by,
                        'updated_by'        => $updated_by,
                        'edit_by'           => $edit_by,
                        'validated_by'      => $validated_by,
                        'validated_at'      => $collection->validated_at,
                        'created_at'        => $collection->created_at,
                        'updated_at'        => $collection->updated_at,
                        'received_at'       => $collection->created_at
                    ]);
                    break;

                case "5":
                    $this->command->info('Audio : ' . $collection->title);
                    $id = \App\Models\Collection::insertGetId([
                        'id_old'            => $collection->id_coll,
                        'publisher_id'      => $collection->publisher_id,
                        'city_id'           => $city ? $city->id : null,
                        'title'             => $this->parseTitle($collection->title)['title'],
                        'slug'              => $checkSlug  > 0 ? \Str::slug($collection->title) . '_' . $checkSlug : \Str::slug($collection->title),
                        'type'              => $collection->id_type,
                        'edition'           => null,
                        'album'             => $this->parseTitle($collection->title)['album'],
                        'serial'            => null,
                        'ddc'               => null,
                        'volume'            => null,
                        'deposit'           => $collection->id_deposit,
                        'code'              => $collection->identifier,
                        'code_type'         => 3, //ISRC
                        'code_kdt'          => null,
                        'source'            => null,
                        'publication_year'  => $date,
                        'received_at'       => $collection->validated_at != "" ? $collection->validated_at : null,
                        'copyright'         => "Copyright (c) " . $date . " " . $publisher_name,
                        'preview'           => "00:00-00:45",
                        'description'        => $collection->description,
                        'problem'           => $problem_lainnya,
                        'lock'              => $collection->is_lock,
                        'manual'            => $collection->is_manual,
                        'status'            => $status,
                        'created_by'        => $created_by,
                        'updated_by'        => $updated_by,
                        'edit_by'           => $edit_by,
                        'validated_by'      => $validated_by,
                        'validated_at'      => $collection->validated_at,
                        'created_at'        => $collection->created_at,
                        'updated_at'        => $collection->updated_at,
                    ]);
                    $this->saveSubject($collection, $id);
                    $this->saveContributor($collection, $id);
                    $this->saveCreator($collection, $id);
                    break;

                default:
                    break;
            }
        }
    }

    public function parseTitle($req)
    {
        $string = explode(';album,', $req);
        if (count($string) > 1) {
            $album = array_key_exists(1, $string) ? trim(str_replace("album,", "", $string[1])) : "";
            $title = substr($string[0], 6, strlen($string[0]));
            return [
                "album"    => $album,
                "title"    => $title
            ];
        } else {
            return [
                "album"    => "",
                "title"    => $req
            ];
        }
    }

    public function saveSubject($coll, $id_coll)
    {
        if (strlen($coll->subject) > 0) {
            $subjects = explode(";", $coll->subject);

            foreach ($subjects as $subject) {
                $countSubject = App\Models\Subject::where('slug', \Str::slug($subject))->count();
                if ($countSubject > 0) {
                    $subjectDbId = App\Models\Subject::where('slug', \Str::slug($subject))->first()->id;
                } else {
                    $subjectDbId = App\Models\Subject::insertGetId([
                        'name'       => $subject,
                        'slug'       => \Str::slug($subject),
                        'created_at' => $coll->created_at,
                        'updated_at' => $coll->updated_at,
                    ]);
                    $this->command->info('Create new subject = ' . $subject);
                }
                App\Models\CollectionSubject::insert([
                    'collection_id'  => $id_coll,
                    'subject_id'     => $subjectDbId,
                    'created_at'     => $coll->created_at,
                    'updated_at'     => $coll->updated_at
                ]);
            }
        }
        if ($coll->id_type == 5) {
            $category = App\Models\Category::where('slug', \Str::slug($coll->type))->get();
            if (count($category) > 0) {
                $categoryId = $category[0]->id;
                App\Models\CollectionCategory::insert([
                    'collection_id' => $id_coll,
                    'category_id'   => $categoryId,
                    'created_at'    => $coll->created_at,
                    'updated_at'    => $coll->updated_at
                ]);
            }
        }
    }

    public function saveContributor($coll, $id_coll)
    {
        if (strlen($coll->contributor) > 0) {
            $contributors = explode(";", $coll->contributor);
            foreach ($contributors as $contributor) {
                if ($contributor != "") {
                    $getContributor = explode(",", $contributor);
                    if (count($getContributor) > 1) {
                        $contributor_db = App\Models\Contributor::firstOrCreate([
                            'slug'  => \Str::slug($getContributor[0]),
                            'type'  => $coll->id_type,
                        ], [
                            'name'          => $getContributor[0],
                            'slug'          => \Str::slug($getContributor[0]),
                            'is_creator'    => 0,
                            'created_at'    => $coll->created_at,
                            'updated_at'    => $coll->created_at,
                        ]);

                        $author = trim(substr($contributor, strlen($getContributor[0]) + 1, strlen($contributor)));
                        $author_db = App\Models\Author::firstOrCreate([
                            'slug'          => \Str::slug($author),
                        ], [
                            'fullname'      => $author,
                            'slug'          => \Str::slug($author),
                            'created_at'    => $coll->created_at,
                            'updated_at'    => $coll->created_at,
                        ]);
                        \App\Models\CollectionContributor::insert([
                            'collection_id'  => $id_coll,
                            'contributor_id' => $contributor_db->id,
                            'author_id'      => $author_db->id,
                        ]);
                    }
                }
            }
        }
    }
    public function saveCreator($coll, $id_coll)
    {
        if (strlen($coll->creator) > 0) {
            switch ($coll->id_type) {
                case 1: //buku
                    $creator_db = App\Models\Contributor::firstOrCreate([
                        'slug'  => 'penulis'
                    ], [
                        'name'          => "Penulis",
                        'type'          => 1,
                        'is_creator'    => 1,
                        'created_at'    => $coll->created_at,
                        'updated_at'    => $coll->created_at,
                    ]);
                    break;

                case 2: // partitur
                    $creator_db = App\Models\Contributor::firstOrCreate([
                        'slug'  => 'komposer'
                    ], [
                        'name'          => "Komposer",
                        'type'          => 2,
                        'is_creator'    => 1,
                        'created_at'    => $coll->created_at,
                        'updated_at'    => $coll->created_at,
                    ]);
                    break;

                case 5: // audio
                    $creator_db = App\Models\Contributor::firstOrCreate([
                        'slug'  => 'pencipta-lagu'
                    ], [
                        'name'          => "Pencipta Lagu",
                        'type'          => 5,
                        'is_creator'    => 1,
                        'created_at'    => $coll->created_at,
                        'updated_at'    => $coll->created_at,
                    ]);
                    break;
                default:
                    break;
            }
            if ($coll->id_type != 4) {
                $author_db = App\Models\Author::firstOrCreate([
                    'slug'  => \Str::slug($coll->creator)
                ], [
                    'fullname'      => $coll->creator,
                    'created_at'    => $coll->created_at,
                    'updated_at'    => $coll->created_at,
                ]);

                \App\Models\CollectionContributor::insert([
                    'collection_id'  => $id_coll,
                    'contributor_id' => $creator_db->id,
                    'author_id'      => $author_db->id,
                    'created_at'     => $coll->created_at,
                    'updated_at'     => $coll->created_at,
                ]);
            }
        }
    }
}
