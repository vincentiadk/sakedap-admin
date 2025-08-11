<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollectionMedia;
use Illuminate\Support\Facades\Storage;

class CheckFileExists extends Command
{

    protected $signature = 'check:file';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $totalFile = 0;
        /*$colls = CollectionMedia::where('type', 2)
            ->whereHas('collection', function($query) {
                $query->where('status',1)
                    ->where('type',1);
            })->get();
        
        foreach($colls as $coll) {
            if(! Storage::disk($coll->location->location)->exists($coll->link)){
                CollectionMedia::where('type', 3)
                ->where('collection_id', $coll->collection_id)
                ->delete();

                $coll->delete();
                $totalFile++;
                $this->line("File not exists " . $coll->collection_id .", " . $totalFile . " file");
            }
        }


        $totalCover = 0;
        $colls2 = CollectionMedia::where('type', 1)
        ->whereHas('collection', function($query) {
            $query->where('status',1)
                ->where('type',1);
        })->get();
        foreach($colls2 as $coll2) {
            if(! Storage::disk($coll2->location->location)->exists($coll2->link)){
                $coll2->delete();
                $totalCover++;
                $this->line("Cover not exists " . $coll2->collection_id .", " . $totalCover . " cover");
            }
        }
        $this->line("Total File not exists : ". $totalFile . " file");
        $this->line("Total Cover not exists : ". $totalCover . " cover");
        */
        $colls = CollectionMedia::where('location_id', 1)->get();
        foreach ($colls as $coll) {
            if ($coll->location->location == 'storage1') {
                if (!Storage::disk($coll->location->location)->exists($coll->link)) {
                    if (Storage::disk('storage2')->exists($coll->link)) {
                        $coll->update([
                            'location_id' => 2
                        ]);
                        $totalFile++;
                        $this->line("Total File lokasi ydb dipindah dari storage 1 ke storage 2 : " . $totalFile . " file");
                    }
                }
            }
        }
        return 0;
    }
}
