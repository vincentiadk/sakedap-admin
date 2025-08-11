<?php

namespace App\Console\Commands;

use App\Helper\GeneralHelper;
use App\Jobs\PDFToImage;
use App\Jobs\WatermarkAudio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\CollectionMedia;

class CreateCollectionMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collectionmedia:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watermark and cut file original';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collection_media = CollectionMedia::where('type', 4)->get();
        foreach ($collection_media as $d) {
            switch ($d->collection->type) {
                case 1:
                    $dir_original = $d->link;
                    $job = new PDFToImage('book', $d->link, $d->collection_id);
                    $job->onQueue('convert_pdf');
                    dispatch($job);
                    break;
                case 2:
                    $dir_original = $d->link;
                    $job = new PDFToImage('partitur', $d->link, $d->collection_id);
                    $job->onQueue('convert_pdf');
                    dispatch($job);
                    break;
                case 3:
                    $dir_original = $d->link;
                    $job = new PDFToImage('map', $d->link, $d->collection_id);
                    $job->onQueue('convert_pdf');
                    dispatch($job);
                    break;
                case 4: //serial
                    $dir_original = $d->link;
                    $job = new PDFToImage('serial', $d->link, $d->collection_id);
                    $job->onQueue('convert_pdf');
                    dispatch($job);
                    break;
                case 5: //audio
                    $dir_original = $d->link;
                    $job = new WatermarkAudio($d->link, $d);
                    $job->onQueue('audio');
                    dispatch($job);
                    break;
                default:
                    break;
            }
        }
    }
}
