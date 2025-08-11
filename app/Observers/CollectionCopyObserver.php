<?php

namespace App\Observers;

use App\Helper\GeneralHelper;
use App\Models\CollectionCopy;

class CollectionCopyObserver
{
    public function __construct()
    {
        // $this->client = new Client(new Curl(), new EventDispatcher(), config('solr'));
        // $this->edepositSync = new EdepositSync(new Indexer(), $this->client);
    }

    /**
     * Handle the collection "creating" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function creating(CollectionCopy $collection_copy)
    {
        $collection_copy->code = GeneralHelper::codeCopyCollection();
    }

    /**
     * Handle the collection "created" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function created(CollectionCopy $collection_copy)
    {
        // try {
        //     if ($collection_copy->status == 2) {
        //         $this->edepositSync->doSynchronizeOne($collection_copy, $this->url);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "updated" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function updated(CollectionCopy $collection_copy)
    {
        // try {
        //     if ($collection_copy->status == 2) {
        //         $this->edepositSync->doSynchronizeOne($collection_copy, $this->url);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "deleted" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function deleted(CollectionCopy $collection_copy)
    {
        // try {
        //     $this->edepositSync->removeCollectionCopy($collection_copy->id);
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "restored" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function restored(CollectionCopy $collection_copy)
    {
        // try {
        //     if ($collection_copy->status == 2) {
        //         $this->edepositSync->doSynchronizeOne($collection_copy, $this->url);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }

    /**
     * Handle the collection "force deleted" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function forceDeleted(CollectionCopy $collection_copy)
    {
        // try {
        //     $this->edepositSync->removeCollectionCopy($collection_copy->id);
        // } catch (\Exception $e) {
        //     Log::error('faield sync edeposit to solr: ' . $e->getMessage());
        // }
    }
}
