<?php

namespace App\Services;

use DB;
use Solarium\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Plugin\BufferedAdd\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;

class EdepositSync
{

    protected $indexer;
    protected $client;
    protected $buffer;
    protected $tableName = 'collections';
    protected $systemName = 'edeposit';

    function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;
        $this->client = new Client(new Curl(), new EventDispatcher(), config('solr'));
        $this->buffer = $this->client->getPlugin('bufferedadd');
    }


    public function doSynchronize($data, $edepositUrl)
    {
        $this->buffer->setBufferSize(count($data));
        $this->buffer->setCommitWithin(count($data));
        $this->buffer->setOverWrite(true);

        foreach ($data as $item) {
            $this->createOrUpdate($item, $edepositUrl);
        }
    }

    public function doSynchronizeOne($data, $edepositUrl)
    {
        $this->buffer->setBufferSize(1);
        $this->buffer->setCommitWithin(1);
        $this->buffer->setOverWrite(true);

        $this->createOrUpdate($data, $edepositUrl);
    }

    private function createOrUpdate($item, $edepositUrl)
    {

        if (count($collection = $this->checkCollections($item->id)) > 0) {
            $this->updateCollections($collection[0], $item);
            return;
        }

        try {

            $update = $this->client->createUpdate();
            $doc = $update->createDocument();
            $doc->table_name = $this->tableName;
            $doc->system_name = $this->systemName;
            $doc->table_id = $item->id;

            $contributor = DB::connection('mysql')
                ->table('collection_contributors')
                ->select(
                    DB::raw('CONCAT(contributors.name, ": ", authors.fullname) AS contibutor_name')
                )
                ->leftJoin('contributors', 'contributors.id', 'collection_contributors.contributor_id')
                ->leftJoin('authors', 'authors.id', 'collection_contributors.author_id')
                ->where('collection_contributors.collection_id', $item->id)
                ->get()->pluck('contibutor_name')->all();

            $subject = DB::connection('mysql')
                ->table('collection_subjects')
                ->select(DB::raw('subjects.name as subject_name'))
                ->leftJoin('subjects', 'subjects.id', 'collection_subjects.subject_id')
                ->where('collection_subjects.collection_id', $item->id)
                ->get()->pluck('subject_name')->all();

            $publisher = DB::connection('mysql')
                ->table('publishers')
                ->select('publishers.name as publisher_name', 'cities.name as city_name', 'provinces.name as province_name')
                ->leftJoin('cities', 'cities.id', 'publishers.city_id')
                ->leftJoin('provinces', 'provinces.id', 'publishers.province_id')
                ->where('publishers.id', $item->publisher_id)
                ->first();

            $cover = DB::connection('mysql')
                ->table('collection_media')
                ->select('collection_media.link')
                ->where('collection_media.type', 1)
                ->where('collection_media.collection_id', $item->id)
                ->first();

            if ($item->type == 1 || $item->type == 2 || $item->type == 3 || $item->type == 4) {
                $file = DB::connection('mysql')
                    ->table('collection_media')
                    ->select('collection_media.link')
                    ->where('collection_media.type', 3)
                    ->where('collection_media.collection_id', $item->id)
                    ->first();
            } else if ($item->type == 5) {
                $file = DB::connection('mysql')
                    ->table('collection_media')
                    ->select('collection_media.link')
                    ->where('collection_media.type', 3)
                    ->where('collection_media.collection_id', $item->id)
                    ->first();
            } else if ($item->type == 6) {
                $file = DB::connection('mysql')
                    ->table('collection_media')
                    ->select('collection_media.link')
                    ->where('collection_media.type', 3)
                    ->where('collection_media.collection_id', $item->id)
                    ->first();
            }

            $json = json_decode($item->physical_description);

            $description_total = '';
            $description_other = '';
            $description_dimension = '';

            if (isset($json)) {
                if (isset($json->total_page)) {
                    $description_total = $json->total_page;
                }
            }

            $link = '';
            if ($cover) {
                $link = $edepositUrl . 'collection/cover/' . $item->id;
            }

            $file_media = '';
            if ($file) {
                if ($item->type == 1 || $item->type == 2 || $item->type == 3 || $item->type == 4) {
                    $file_media = $edepositUrl . 'collection-iframe/' . $item->id;
                } else {
                    $file_media = $edepositUrl . $file->link;
                }
            }



            $jenis = '';
            if ($item->type == 1) {
                $jenis = 'Buku';
            } else if ($item->type == 2) {
                $jenis = 'Partitur';
            } else if ($item->type == 3) {
                $jenis = 'Peta';
            } else if ($item->type == 4) {
                $jenis = 'Serial';
            } else if ($item->type == 5) {
                $jenis = 'Audio';
            } else if ($item->type == 6) {
                $jenis = 'Film';
            } else {
                $jenis = null;
            }

            $doc->table_id = $item->id;
            $doc->title = $item->title;
            $doc->type = $item->type;
            $doc->jenis = $jenis;
            $doc->ddc = $item->ddc;
            $doc->sub_title =  '';
            $doc->creator = $contributor;
            $doc->creator_string = $contributor;
            $doc->description_total = $description_total;
            $doc->description_other = $description_other;
            $doc->description_dimension = $description_dimension;
            $doc->subject = $subject;
            $doc->subject_string = $subject;
            $doc->city_name = $publisher->city_name;
            $doc->province_name = $publisher->province_name;
            $doc->publication_place = $publisher->city_name;
            $doc->publisher_string = $publisher->publisher_name;
            $doc->publisher = $publisher->publisher_name;
            $doc->year_string = $item->publication_year;
            $doc->publication_year = $item->publication_year;
            $doc->code = $item->code;
            $doc->code_parsing = str_replace('-', '', $item->code);
            $doc->cover = $link;
            $doc->link_original = $file_media;
            $doc->description = $item->description;
            $doc->rights = $item->copyright;
            $doc->deposit = $item->deposit;
            $doc->created_at = date('Y-m-d\Th:m:s\Z', strtotime($item->created_at));
            $doc->updated_at = date('Y-m-d\Th:m:s\Z', strtotime($item->updated_at));
            $doc->deleted_at = date('Y-m-d\Th:m:s\Z', strtotime($item->deleted_at));
            $doc->received_date = date('Y-m-d\Th:m:s\Z', strtotime($item->received_at));

            $update->addDocument($doc);
            $update->addCommit();
            $this->client->update($update);
        } catch (\Exception $e) {
            \Log::error('error indexing edeposit: message -  ' . $e->getMessage() . '\n' . json_encode($item));
        }
    }

    private function updateCollections($collection, $item)
    {


        try {
            $update = $this->client->createUpdate();
            $doc = $update->createDocument();
            $doc->setKey('id', $collection['id']);

            $contributor = DB::connection('mysql')
                ->table('collection_contributors')
                ->select(
                    DB::raw('CONCAT(contributors.name, ": ", authors.fullname) AS contibutor_name')
                )
                ->leftJoin('contributors', 'contributors.id', 'collection_contributors.contributor_id')
                ->leftJoin('authors', 'authors.id', 'collection_contributors.author_id')
                ->where('collection_contributors.collection_id', $item->id)
                ->get()->pluck('contibutor_name')->all();

            $subject = DB::connection('mysql')
                ->table('collection_subjects')
                ->select(DB::raw('subjects.name as subject_name'))
                ->leftJoin('subjects', 'subjects.id', 'collection_subjects.subject_id')
                ->where('collection_subjects.collection_id', $item->id)
                ->get()->pluck('subject_name')->all();

            $publisher = DB::connection('mysql')
                ->table('publishers')
                ->select('publishers.name as publisher_name', 'cities.name as city_name', 'provinces.name as province_name')
                ->leftJoin('cities', 'cities.id', 'publishers.city_id')
                ->leftJoin('provinces', 'provinces.id', 'publishers.province_id')
                ->where('publishers.id', $item->publisher_id)
                ->first();

            $cover = DB::connection('mysql')
                ->table('collection_media')
                ->select('collection_media.link')
                ->where('collection_media.type', 1)
                ->where('collection_media.collection_id', $item->id)
                ->first();

            if ($item->type == 1 || $item->type == 2 || $item->type == 3 || $item->type == 4) {
                $file = DB::connection('mysql')
                    ->table('collection_media')
                    ->select('collection_media.link')
                    ->where('collection_media.type', 3)
                    ->where('collection_media.collection_id', $item->id)
                    ->first();
            } else if ($item->type == 5) {
                $file = DB::connection('mysql')
                    ->table('collection_media')
                    ->select('collection_media.link')
                    ->where('collection_media.type', 3)
                    ->where('collection_media.collection_id', $item->id)
                    ->first();
            } else if ($item->type == 6) {
                $file = DB::connection('mysql')
                    ->table('collection_media')
                    ->select('collection_media.link')
                    ->where('collection_media.type', 3)
                    ->where('collection_media.collection_id', $item->id)
                    ->first();
            }


            $json = json_decode($item->physical_description);

            $description_total = '';
            $description_other = '';
            $description_dimension = '';

            if (isset($json)) {
                if (isset($json->total_page)) {
                    $description_total = $json->total_page;
                }
            }

            $link = '';
            if ($cover) {
                $link = config('website.deposit_url') . 'collection/cover/' . $item->id;
            }

            $file_media = '';
            if ($file) {
                if ($item->type == 1 || $item->type == 2 || $item->type == 3 || $item->type == 4) {
                    $file_media = config('website.deposit_url') . 'collection-iframe/' . $item->id;
                } else {
                    $file_media = config('website.deposit_url') . $file->link;
                }
            }

            $jenis = '';
            if ($item->type == 1) {
                $jenis = 'Buku';
            } else if ($item->type == 2) {
                $jenis = 'Partitur';
            } else if ($item->type == 3) {
                $jenis = 'Peta';
            } else if ($item->type == 4) {
                $jenis = 'Serial';
            } else if ($item->type == 5) {
                $jenis = 'Audio';
            } else if ($item->type == 6) {
                $jenis = 'Film';
            } else {
                $jenis = null;
            }

            $doc->setField('title', $item->title,  null, $doc::MODIFIER_SET);
            $doc->setField('type', $item->type,  null, $doc::MODIFIER_SET);
            $doc->setField('jenis', $jenis,  null, $doc::MODIFIER_SET);
            $doc->setField('ddc', $item->ddc,  null, $doc::MODIFIER_SET);
            $doc->setField('sub_title', '',  null, $doc::MODIFIER_SET);
            $doc->setField('contributor', $contributor,  null, $doc::MODIFIER_SET);
            $doc->setField('contributor_string', $contributor,  null, $doc::MODIFIER_SET);
            $doc->setField('description_total', $description_total,  null, $doc::MODIFIER_SET);
            $doc->setField('description_other', $description_other,  null, $doc::MODIFIER_SET);
            $doc->setField('description_dimension', $description_dimension,  null, $doc::MODIFIER_SET);
            $doc->setField('subject', $subject,  null, $doc::MODIFIER_SET);
            $doc->setField('subject_string', $subject,  null, $doc::MODIFIER_SET);
            $doc->setField('city_name', $publisher->city_name,  null, $doc::MODIFIER_SET);
            $doc->setField('province_name', $publisher->province_name,  null, $doc::MODIFIER_SET);
            $doc->setField('publication_place', $publisher->city_name,  null, $doc::MODIFIER_SET);
            $doc->setField('publisher_string', $publisher->publisher_name,  null, $doc::MODIFIER_SET);
            $doc->setField('publisher', $publisher->publisher_name,  null, $doc::MODIFIER_SET);
            $doc->setField('year_string', $item->publication_year,  null, $doc::MODIFIER_SET);
            $doc->setField('publication_year', $item->publication_year,  null, $doc::MODIFIER_SET);
            $doc->setField('code', str_replace('-', '', $item->code),  null, $doc::MODIFIER_SET);
            $doc->setField('code_parsing', $item->code,  null, $doc::MODIFIER_SET);
            $doc->setField('cover_url', $link,  null, $doc::MODIFIER_SET);
            $doc->setField('link_original', $file_media,  null, $doc::MODIFIER_SET);
            $doc->setField('description', $item->description,  null, $doc::MODIFIER_SET);
            $doc->setField('rights', $item->copyright,  null, $doc::MODIFIER_SET);
            $doc->setField('deposit', $item->deposit,  null, $doc::MODIFIER_SET);
            $doc->setField('created_at', date('Y-m-d\Th:m:s\Z', strtotime($item->created_at)),  null, $doc::MODIFIER_SET);
            $doc->setField('updated_at', date('Y-m-d\Th:m:s\Z', strtotime($item->updated_at)),  null, $doc::MODIFIER_SET);
            $doc->setField('deleted_at', date('Y-m-d\Th:m:s\Z', strtotime($item->deleted_at)),  null, $doc::MODIFIER_SET);
            $doc->setField('received_date', date('Y-m-d\Th:m:s\Z', strtotime($item->received_at)),  null, $doc::MODIFIER_SET);

            // $update->addDeleteById($collection['id']);
            $update->addCommit();
            $this->client->update($update);
        } catch (\Exception $e) {
            \Log::error('error indexing edeposit: message -  ' . $e->getMessage() . '\n' . json_encode($item));
        }
    }

    private function setAdditionalItem($doc, $item) {}

    private function checkCollections($tableId)
    {
        return $this->indexer->searchById($this->tableName, $this->systemName, $tableId);
    }

    public function removeCollection($tableId)
    {
        // get an update query instance
        $update = $client->createUpdate();

        // add the delete query and a commit command to the update query
        $update->addDeleteQuery('sytem_name:edeposit AND table_id:' . $table_id);
        $update->addCommit();

        // this executes the query and returns the result
        $result = $client->update($update);
    }
}
