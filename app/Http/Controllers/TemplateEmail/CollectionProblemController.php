<?php

namespace App\Http\Controllers\TemplateEmail;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionProblemController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug = 'template-email-collection-problem'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $request->content
                ]);
            } else {
                QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'template-email-collection-problem'
                ]);
            }

            return redirect('template-email/collection-problem')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        $data = [
            'template' => $template,
            'content' => 'template-email.collection-problem'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
