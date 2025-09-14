<?php

namespace App\Http\Controllers\TemplateEmail;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublisherAcceptController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug = 'template-email-publisher-success'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $request->content
                ]);
            } else {
                QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'template-email-publisher-success'
                ]);
            }

            return redirect('template-email/publisher-accept')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        $data = [
            'template' => $template,
            'content' => 'template-email.publisher-accept'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
