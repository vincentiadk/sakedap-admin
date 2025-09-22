<?php

namespace App\Http\Controllers\Setting;

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
                    'value' => $request->content,
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);
            } else {
                QueryAPI::create('settingparameters', [
                    'value' => $request->content,
                    'name' => 'TentangKamiEdeposit',
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'createby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'updateby' => session('name'),
                ], false);
            }

            return redirect('setting/about-us')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        return view('layouts.index', [
            'data' => [
                'template' => $template,
                'content' => 'setting.about-us',
                'plugins' => [
                    'ckeditor',
                ]
            ]
        ]);
    }
}
