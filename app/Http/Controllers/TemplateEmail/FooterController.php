<?php

namespace App\Http\Controllers\TemplateEmail;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FooterController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug = 'template-email-footer'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $request->content
                ]);
            } else {
                $template = QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'template-email-footer'
                ]);
            }

            $upload = QueryAPI::uploadFile([
                'type' => 'gambar_template',
                'id' => $template->ID ?? null,
                'iszip' => 0,
                'file' => $request->file('file'),
            ]);

            if ($upload) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $upload->FileName
                ]);
            }

            return redirect('template-email/footer')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        $data = [
            'template' => $template,
            'content' => 'template-email.footer'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
