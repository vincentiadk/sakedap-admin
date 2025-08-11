<?php

namespace App\Observers;

use App\Models\Library;
use Illuminate\Support\Str;

class LibraryObserver
{
    public function __construct()
    {
        // $this->client = new Client(new Curl(), new EventDispatcher(), config('solr'));
        // $this->edepositSync = new EdepositSync(new Indexer(), $this->client);
    }

    /**
     * Handle the collection "creating" event.
     *
     * @param  \App\Library  $library
     * @return void
     */
    public function creating(Library $library)
    {
        $library->api_key = Str::random(64);
    }


    /**
     * Handle the collection "updated" event.
     *
     * @param  \App\CollectionCopy  $collection_copy
     * @return void
     */
    public function updated(Library $library)
    {
        if (empty($library->api_key)) {
            $library->api_key = Str::random(64);
        }
    }
}
