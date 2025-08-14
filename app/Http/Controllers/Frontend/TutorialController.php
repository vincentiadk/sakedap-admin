<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tutorial;

class TutorialController extends Controller
{
    public function index()
    {

        $tutorial = Tutorial::where('publish', '1')->orderBy('category', 'asc')->orderBy('sequence', 'asc')->get();
        $new_faq = [];
        foreach ($tutorial as $key => $value) {
            $new_faq[$value['category']][] = $value;
        }

        $tutorial = $new_faq;

        $data = [
            'title'       => 'Tutorial Edeposit - National Library of Indonesia',
            'tutorial'        => $tutorial,
            'content'     => 'frontend.tutorial'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }

    public function detail($slug)
    {
        $tutorial = Tutorial::where('slug', $slug)->firstOrFail();

        $otherTutorial = Tutorial::where('publish', 1)->where('id', '<>', $tutorial->id)->limit(10)->orderBy('created_at', 'desc')->get();

        $data = [
            'title'                => $tutorial->title,
            'tutorial'                 => $tutorial,
            'othertutorial'          => $otherTutorial,
            'content'              => 'frontend.tutorial_detail'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }
}
