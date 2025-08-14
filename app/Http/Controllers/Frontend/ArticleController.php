<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class ArticleController extends Controller
{
    public function index()
    {

        $news = News::where('status', 2)->orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'title'       => 'Artikel - Edeposit - National Library of Indonesia',
            'news'        => $news,
            'content'     => 'frontend.article'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }

    public function detail($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        $otherNews = News::where('status', 2)->where('id', '<>', $news->id)->limit(10)->orderBy('created_at', 'desc')->get();

        $data = [
            'title'                => $news->title,
            'news'                 => $news,
            'othernews'          => $otherNews,
            'content'              => 'frontend.article_detail'
        ];

        return view('frontend.layout.index', ['data' => $data]);
    }
}
