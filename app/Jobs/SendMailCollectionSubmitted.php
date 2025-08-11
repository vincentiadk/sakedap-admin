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

class SendMailCollectionSubmitted implements ShouldQueue
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
        $template = Setting::where('slug', 'template-email-collection-submitted')->first();

        try {
            $data = $this->params;
            Mail::send([], [], function ($message) use ($data, $template) {

                $message->to($data['email'], 'edeposit@perpusnas.go.id')
                    ->subject('Koleksi Diterima')
                    ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                    ->setBody($template->parse($data), 'text/html');
            });
        } catch (\Exception $e) {
        }

        Notification::create([
            'user_id'   => $data['user_id'],
            'title'     => 'Koleksi Diterima',
            'body'      => $template->parse($data)
        ]);
    }
}
