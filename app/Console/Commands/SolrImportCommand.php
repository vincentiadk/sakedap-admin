<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SolrImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Import Data SQLSERVER to Solr';

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
        $response = Http::asForm()->post('https://solr-kckr.perpusnas.go.id/solr/isbn/dataimport?indent=on&wt=json', [
            'command'  => 'full-import',
            'verbose'  => false,
            'clean'    => true,
            'commit'   => true,
            'optimize' => true,
            'core'     => 'isbn',
            'name'     => 'dataimport'
        ]);

        return;
    }
}
