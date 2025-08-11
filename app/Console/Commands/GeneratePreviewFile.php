<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PDFToImage;
use App\Models\CollectionMedia;
use App\Models\Collection;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;

class GeneratePreviewFile extends Command
{

    protected $signature = 'generate:preview';

    protected $description = 'Regenerate Faield Preview File';

    protected $location;

    public function __construct()
    {
        parent::__construct();
        $this->location = Location::where('active', 1)->first();
    }


    public function handle()
    {
        /*$colls = Collection::where('status',1)->where('type',1)->get();
        $num = 0;
        foreach($colls as $coll){
            if(! $coll->collectionMedia->where('type', 3)->first()){
                $cMedia = $coll->collectionMedia->where('type', 2)->first();
                if($cMedia){
                    $count = count(Storage::disk($cMedia->location->location)->files('collection/book/images/' . $coll->id));
                    if($count > 0) {
                        $this->line("gak ada images di db " . $coll->id . " tapi file nya ada " . $count );
                        $num++;
                        
                        $arr       = explode('/', $cMedia->link);
                        $file_name = str_replace(".pdf", "", end($arr));
                        $media = CollectionMedia::firstOrNew([
                            'collection_id' => $coll->id,
                            'link'          => 'collection/book/images/'.$coll->id.'/' . $file_name,
                            'type'          => 3,
                            'method'        => 4,
                            'status'        => 1,
                        ], [
                            'location_id' => $this->location->id,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        ]);
                        $media->save();
                    }
                }
            }
        }
        $this->line(" total " . $num);
        */
        $colls = CollectionMedia::where('type', 2)
            ->where('extension', 'pdf')
            ->whereHas('collection', function ($query) {
                $query->where('status', 1)
                    ->where('type', 1);
            })->get();

        foreach ($colls as $coll) {
            $preview = CollectionMedia::where('collection_id', $coll->collection_id)->where('type', 3)->first();
            if (!$preview) {
                //$this->line("belum ada preview di db  " . $coll->collection_id . ' original ' .$coll->link);
                $type = $coll->collection->type();
                Storage::disk($coll->location->location)->deleteDirectory('collection/' . $type . '/images/' . $coll->collection_id);
                $job = new PDFToImage($type, $coll->link, $coll->collection_id);
                $this->line("creating job PDFToImage for " . $coll->collection_id . " jenis : " . $type);
                dispatch(($job)->onQueue('convert_pdf'));
            }
        }
        /*
        $colls = CollectionMedia::where('type', 3)
        ->whereHas('collection', function($query) {
            $query->where('status',1) 
            ->where('type', 1);
        })->get();

        foreach($colls as $coll) {
            if(count($coll->jsonParse()) == 0){
                $this->line("ada di db tapi ga ada file preview" . $coll->collection_id);
                //Storage::disk($coll->location->location)->deleteDirectory('collection/book/images/' . $coll->collection_id);
                //$job = new PDFToImage('book', $coll->link, $coll->collection_id);
                //$this->line("creating job PDFToImage for " . $coll->collection_id);
                //dispatch(($job)->onQueue('convert_pdf'));
            }
        }*/
        return 0;
    }
}
