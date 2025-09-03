<?php

namespace App\Http\Controllers\Setting;

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
                e_settings
            where
                slug = 'terms-conditions'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $request->content
                ]);
            } else {
                QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'terms-conditions'
                ]);
            }

            return redirect('setting/terms-conditions')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        $data = [
            'template' => $template,
            'content' => 'setting.terms-conditions'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
