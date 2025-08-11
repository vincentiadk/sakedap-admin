<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DesktopToken;
use Hash;
use Str;

class VersionController extends Controller
{

	public function index()
	{
		return response()->json([
			'message'	=> 'success',
			'version'	=> '1.0.0'
		]);
	}
}
