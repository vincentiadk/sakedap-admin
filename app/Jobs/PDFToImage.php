<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper\GeneralHelper;

class PDFToImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $type;
    private $dirOriginal;
    private $collectionId;
    public $timeout = 86400;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $dirOriginal, $collectionId)
    {
        $this->type = $type;
        $this->dirOriginal = $dirOriginal;
        $this->collectionId = $collectionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


       // try {
            GeneralHelper::pdfToImage($this->type, $this->dirOriginal, $this->collectionId);
       // } catch (\Exception $e) {

       // }
    }
}
