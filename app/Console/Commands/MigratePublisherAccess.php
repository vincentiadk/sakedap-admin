<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Publisher;
use App\Models\PublisherAccess;

class MigratePublisherAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publisher:access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi table publisher ke table publisher access';

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
        $publisher = Publisher::select('id', 'system_type', 'code_system')->get();

        foreach ($publisher as $item) {
            PublisherAccess::create([
                'publisher_id'      => $item->id,
                'system_type'       => $item->system_type,
                'code_system'       => $item->code_system
            ]);
        }
    }
}
