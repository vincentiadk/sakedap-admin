<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TermsConditionsController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                settingparameters
            where
                name = 'SyaraKetentuanEdeposit'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('settingparameters', ($template->ID ?? null), [
                    '!value_lob' => $request->content,
                    'updateby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                ], false);
            } else {
                QueryAPI::create('settingparameters', [
                    '!value_lob' => $request->content,
                    'name' => 'SyaraKetentuanEdeposit',
                    'createby' => session('name'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'createby' => session('name'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'updateby' => session('name'),
                ], false);
            }

            return redirect('administration-system/terms-conditions')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        return view('layouts.index', [
            'data' => [
                'template' => $template,
                'content' => 'administration-system.terms-conditions',
                'plugins' => [
                    'ckeditor',
                ]
            ]
        ]);
    }
}
