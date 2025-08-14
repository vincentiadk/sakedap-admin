<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {

        $faq = Faq::where('publish', '1')->orderBy('category', 'asc')->orderBy('sequence', 'asc')->get();
        foreach ($faq as $key => $value) {
            $new_faq[$value['category']][] = $value;
        }

        $faq = $new_faq;

        $data = [
            'title'       => 'FAQ Edeposit - National Library of Indonesia',
            'faq'        => $faq,
            'content'     => 'frontend.faq'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }
}
