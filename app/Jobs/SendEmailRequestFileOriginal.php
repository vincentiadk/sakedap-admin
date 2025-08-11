<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Models\Setting;
use App\Models\Notification;

class SendEmailRequestFileOriginal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->params;
        $template = Setting::where('slug', 'template-email-request-file-original')->first();
        Mail::send([], [], function ($message) use ($data, $template) {

            $message->to($data['email'], 'edeposit@perpusnas.go.id')
                ->subject('Download File Original')
                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                ->setBody($template->parse($data), 'text/html');
        });

        Notification::create([
            'user_id'   => $data['user_id'],
            'title'     => 'Download File Original',
            'body'      => $template->parse($data)
        ]);
    }
}
