<?php

namespace App\Jobs;

use App\Models\Bulk;
use Maatwebsite\Excel\Excel;
use Illuminate\Bus\Queueable;
use App\Imports\CollectionImport;
use App\Imports\CollectionKckraImport;
use App\Models\DepositHead;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BatchCollection implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $deposit_head = DepositHead::find($this->request->type);

        $file_excel = storage_path('app/' . $this->request->file_excel);
        if ($deposit_head->category == 'KC' || $deposit_head->category == 'KRA') {
            $import = (new CollectionKckraImport($this->request))->import($file_excel, null, Excel::XLSX);
        } else {
            $import = (new CollectionImport($this->request))->import($file_excel, null, Excel::XLSX);
        }


        if (!$import) {
            Bulk::find($this->request->bulk_id)->update([
                'process_finish_at' => date('Y-m-d H:i:s'),
                'status'            => 3
            ]);
        }

        return true;
    }
}
