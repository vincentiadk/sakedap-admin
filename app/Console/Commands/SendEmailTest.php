<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\TestEmailSMTP;
use Mail;

class SendEmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail {--user=}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a marketing email to a user';

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
        $email = new TestEmailSMTP();        
        Mail::to($this->option('user'))->send($email);
    }
}
