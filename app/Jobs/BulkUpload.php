<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\Trackable;
use Zip;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BookImport;
use App\Imports\AudioImport;
use App\Imports\PartiturImport;
use App\Imports\FilmImport;
use App\Imports\MapImport;
use App\Imports\SerialImport;
use Log;
use App\Models\Publisher;
use App\Models\User;
use App\Models\Location;
use App\Jobs\SendMailCollectionBulkSubmitted;
use Storage;


class BulkUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private $params;
    protected $location;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->prepareStatus();
        $this->params = $params;
        $this->setInput($this->params);
        $this->location = Location::where('active', 1)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $this->setProgressMax(100);

        try {

            $this->setProgressNow(20);

            $fileName = Storage::disk($this->location->location)->path('public/tmp/' . $this->params['file_zip']);
            $fileNameWithoutType = str_replace('.zip', '', $this->params['file_zip']);
            $extractPath = Storage::disk($this->location->location)->path('public/tmp/' . $fileNameWithoutType);

            \Log::debug('extractPath: ' . $extractPath);
            \Log::debug('fileName: ' . $fileName);

            $zip = Zip::open($fileName);
            $zip->extract($extractPath);
            $zip->close();

            $this->setProgressNow(50);

            $sessionName = date('bookupload_' . date('Ymdhis') . '_' . $this->params['user_id']);
            $sessionNameFailed = date('bookupload_failed_' . date('Ymdhis') . '_' . $this->params['user_id']);


            if ($this->params['type_id'] == 1) {
                $excelFile = $extractPath . '/book_bulk_admin.xlsx';
                Excel::import(new BookImport($fileNameWithoutType, $this->params['user_id'], $sessionName, $sessionNameFailed, $this->params['type_of_collection']), $excelFile);
            } else if ($this->params['type_id'] == 2) {
                $excelFile = $extractPath . '/partitur_bulk_admin.xlsx';
                Excel::import(new PartiturImport($fileNameWithoutType,  $this->params['user_id'], $sessionName), $excelFile);
            } else if ($this->params['type_id'] == 3) {
                $excelFile = $extractPath . '/peta_bulk_admin.xlsx';
                Excel::import(new MapImport($fileNameWithoutType,  $this->params['user_id'], $sessionName), $excelFile);
            } else if ($this->params['type_id'] == 5) {
                $excelFile = $extractPath . '/audio_bulk_admin.xlsx';
                Excel::import(new AudioImport($fileNameWithoutType,  $this->params['user_id'], $sessionName), $excelFile);
            } else if ($this->params['type_id'] == 6) {
                $excelFile = $extractPath . '/video_bulk_admin.xlsx';
                Excel::import(new FilmImport($fileNameWithoutType,  $this->params['user_id'], $sessionName), $excelFile);
            } else if ($this->params['type_id'] == 4) {
                $excelFile = $extractPath . '/serial_bulk_admin.xlsx';
                Excel::import(new SerialImport($fileNameWithoutType,  $this->params['user_id'], $this->params['collectionId'], $sessionName), $excelFile);
            }

            $this->setProgressNow(75);

            $user = User::find($this->params['user_id']);

            $params = [
                'user_id'   => $user->id,
                'publisher' => $user->publisher->name,
                'email'     => $user->publisher->email,
                'item'      => session("$sessionName")
            ];

            $job = new SendMailCollectionBulkSubmitted($params);
            dispatch(($job)->onQueue('notification'));

            unlink($fileName);

            $this->setProgressNow(100);
            $this->setOutput(['message' => 'success', 'error' => session("$sessionNameFailed")]);
        } catch (\Exception $e) {
            $this->setOutput(['message' => $e->getMessage()]);
        }
    }
}
