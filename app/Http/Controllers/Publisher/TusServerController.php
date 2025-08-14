<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TusServerController extends Controller
{
    public function index(Request $request) {
    	$response = app('tus-server')->serve();
      // if (!empty($response->headers->get('location')) && config('app.env') === 'production') {
      //   $response->headers->set('location', str_replace('http:', 'https:', $response->headers->get('location')));
      // }
		  return $response->send();
    }
}