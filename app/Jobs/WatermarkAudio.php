<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use App\Models\Location;

class WatermarkAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $dirOriginal;
    private $collMedia;
    protected $location;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dirOriginal, CollectionMedia $collMedia)
    {
        $this->dirOriginal = $dirOriginal;
        $this->collMedia = $collMedia;
        $this->location = Location::where('active', 1)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Storage::disk($this->location->location)->makeDirectory('public/collection/audio/preview/' . $this->collMedia->collection_id);
        Storage::disk($this->location->location)->makeDirectory('public/collection/audio/watermark/' . $this->collMedia->collection_id);

        $prev = explode('-', $this->collMedia->collection->preview);
        $prev_start = $prev[0];
        $prev_end = $prev[1];

        $filename_preview = \Str::random(40) . '.' . $this->collMedia->extension;
        $path_preview =  Storage::disk($this->location->location)->path('public/collection/audio/preview/' . $this->collMedia->collection_id . '/' . $filename_preview);

        $filename_watermark = \Str::random(40) . '.mp3';
        $path_watermark =  Storage::disk($this->location->location)->path('public/collection/audio/watermark/' . $this->collMedia->collection_id . '/' . $filename_watermark);

        $link_collection_preview = 'public/collection/audio/preview/' . $this->collMedia->collection_id . '/' . $filename_preview;
        $link_collection_watermark = 'public/collection/audio/watermark/' . $this->collMedia->collection_id . '/' . $filename_watermark;

        GeneralHelper::audioCut($this->dirOriginal, $path_preview, $prev_start, $prev_end);
        GeneralHelper::audioWatermark($path_preview, $path_watermark);


        $collectionMedia  = CollectionMedia::where('collection_id', $this->collMedia->collection_id)
            ->where('type', 5)
            ->first();

        if ($collectionMedia) {
            $collectionMedia->update([
                'link'              => $link_collection_preview,
                'size'              => File::size($path_preview),
                'extension'         => $this->collMedia->extension,
                'mimes'             => File::mimeType($path_preview),
                'hash'              => md5_file($path_preview),
                'type'              => 5,
                'status'            => 1,
                'method'            => $this->collMedia->method,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        } else {
            CollectionMedia::create([
                'collection_id'         => $this->collMedia->collection_id,
                'link'                  => $link_collection_preview,
                'size'                  => File::size($path_preview),
                'extension'             => $this->collMedia->extension,
                'mimes'                 => File::mimeType($path_preview),
                'hash'                  => md5_file($path_preview),
                'type'                  => 5,
                'status'                => 1,
                'method'                => $this->collMedia->method,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),

            ]);
        }

        $collectionMedia  = CollectionMedia::where('collection_id', $this->collMedia->collection_id)
            ->where('type', 6)
            ->first();

        if ($collectionMedia) {
            $collectionMedia->update([
                'link'              => $link_collection_watermark,
                'size'              => File::size($path_watermark),
                'extension'         => $this->collMedia->extension,
                'mimes'             => File::mimeType($path_watermark),
                'hash'              => md5_file($path_watermark),
                'type'              => 6,
                'status'            => 1,
                'method'            => $this->collMedia->method,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        } else {
            CollectionMedia::create([
                'collection_id'         => $this->collMedia->collection_id,
                'link'                  => $link_collection_watermark,
                'size'                  => File::size($path_watermark),
                'extension'             => $this->collMedia->extension,
                'mimes'                 => File::mimeType($path_watermark),
                'hash'                  => md5_file($path_watermark),
                'type'                  => 6,
                'method'                => $this->collMedia->method,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),

            ]);
        }
    }
}
