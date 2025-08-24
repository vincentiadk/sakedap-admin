<?php

namespace App\Http\Controllers\TemplateEmail;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $template = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug = 'template-email-delivery-receipt'
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $request->content
                ]);
            } else {
                QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'template-email-delivery-receipt'
                ]);
            }

            return redirect('template-email/receipt')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        $data = [
            'template' => $template,
            'content' => 'template-email.receipt'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
