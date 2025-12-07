<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutUsController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                settingparameters
            where
                name = 'TentangKamiEdeposit'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('settingparameters', ($template->ID ?? null), [
                    '!value_lob' => $request->content,
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);
            } else {
                QueryAPI::create('settingparameters', [
                    '!value_lob' => $request->content,
                    'name' => 'TentangKamiEdeposit',
                    'createby' => session('username'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'createby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'updateby' => session('username'),
                ], false);
            }

            return redirect('administration-system/about-us')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        return view('layouts.index', [
            'data' => [
                'template' => $template,
                'content' => 'administration-system.about-us',
                'plugins' => [
                    'ckeditor',
                ]
            ]
        ]);
    }
}
