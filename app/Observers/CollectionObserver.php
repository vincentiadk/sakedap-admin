<?php

namespace App\Observers;

use App\Helper\GeneralHelper;
use Solarium\Client;
use App\Models\Collection;
use App\Models\Publisher;
use App\Models\User;
use App\Services\Indexer;
use App\Services\EdepositSync;
use Illuminate\Support\Facades\Log;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CollectionObserver
{

    protected $client;
    protected $edepositSync;
    private $url = 'https://edeposit.perpusnas.go.id/';

    public function __construct()
    {
        // $this->client = new Client(new Curl(), new EventDispatcher(), config('solr'));
        // $this->edepositSync = new EdepositSync(new Indexer(), $this->client);
    }

    /**
     * Handle the collection "creating" event.
     *
     * @param  \App\Collection  $collection
     * @return void
     */
    public function creating(Collection $collection)
    {
        $publisher = Publisher::find($collection->publisher_id);
        $province_id = isset($publisher->province_id) ? $publisher->province_id : null;
        $city_id = isset($publisher->city_id) ? $publisher->city_id : null;

        Log::debug($collection);
        $create_by = User::find($collection->created_by);
        Log::debug($create_by);
        if (empty($collection->deposit_head_id) && !empty($collection->type)) {
            $collection->deposit_head_id = $collection->type;
        }

        if (empty($collection->type) && !empty($collection->deposit_head_id)) {
            $collection->type = $collection->deposit_head_id;
        }
        if ($create_by->userable_type == 'admins') {
            if ($create_by->library_id == 1) {
                $collection->mark_national = GeneralHelper::generateMarks($collection->deposit_head_id, $province_id);
            } else if ($create_by->library_id != 1 && $create_by->library_id != '' && $create_by->library_id != null) {
                $collection->mark_province = GeneralHelper::generateMarks($collection->deposit_head_id, $province_id, $city_id);
            }
        } else {
            $collection->mark_national = GeneralHelper::generateMarks($collection->deposit_head_id, $province_id);
        }
    }

    /**
     * Handle the collection "created" event.
     *
     * @param  \App\Collection  $collection
     * @return void
     */
    public function created(Collection $collection)
    {
        // try {
        //     if ($collection->status == 2) {
        //         $this->edepositSync->doSynchronizeOne($collection, $this->url);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "updated" event.
     *
     * @param  \App\Collection  $collection
     * @return void
     */
    public function updated(Collection $collection)
    {
        // try {
        //     if ($collection->status == 2) {
        //         $this->edepositSync->doSynchronizeOne($collection, $this->url);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "deleted" event.
     *
     * @param  \App\Collection  $collection
     * @return void
     */
    public function deleted(Collection $collection)
    {
        // try {
        //     $this->edepositSync->removeCollection($collection->id);
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "restored" event.
     *
     * @param  \App\Collection  $collection
     * @return void
     */
    public function restored(Collection $collection)
    {
        // try {
        //     if ($collection->status == 2) {
        //         $this->edepositSync->doSynchronizeOne($collection, $this->url);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "force deleted" event.
     *
     * @param  \App\Collection  $collection
     * @return void
     */
    public function forceDeleted(Collection $collection)
    {
        // try {
        //     $this->edepositSync->removeCollection($collection->id);
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }
}
