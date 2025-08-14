<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    public function createImport(Request $request) {

		$data = [
            'title'   => 'Book',
            'content' => 'publisher.book.import'
        ];

        return view('publisher.layout.index', ['data' => $data]);

    }
}
