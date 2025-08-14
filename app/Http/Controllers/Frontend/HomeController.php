<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Faq;
use App\Models\News;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cache;

class HomeController extends Controller
{
    public function index()
    {

        $news = News::where('status', 2)
            ->take(4)->orderBy('created_at', 'desc')->get();
        $banner = Banner::where('status', 1)->get();
        if (! Cache::has('faq_all')) {
            $faq = Faq::all();
            Cache::put('faq_all', $faq, 3600);
        } else {
            $faq = Cache::get('faq_all');
        }

        $data = [
            'title'           => 'Edeposit - National Library of Indonesia',
            'news'            => $news,
            'banner'        => $banner,
            'faq'           => $faq,
            'content'         => 'frontend.home'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }
}
