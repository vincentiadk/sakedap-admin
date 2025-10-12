<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FooterEmailController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug = 'Footer'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $request->content
                ]);
            } else {
                $template = QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'Footer'
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

            return redirect('administration-system/footer-email')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        return view('layouts.index', [
            'data' => [
                'template' => $template,
                'content' => 'administration-system.footer-email',
                'plugins' => [
                    'fileinput',
                ]
            ]
        ]);
    }
}
