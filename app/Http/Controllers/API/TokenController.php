<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DesktopToken;
use Hash;
use Str;

class TokenController extends Controller
{
	public function authentication(Request $request)
	{

		$username = $request->username;
		$password = $request->password;
		$query    = User::where("username", $username)
			->where('userable_type', 'admins')
			->whereNotNull('verification_at');

		if ($query->count() > 0) {
			if (Hash::check($password, $query->first()->password)) {

				$token = Str::random(60);
				$expired_at = Date('Y-m-d H:i:s', strtotime('+30 days'));

				DesktopToken::where('ip_address', $request->ip_address)->delete();

				DesktopToken::create([
					'ip_address'		=> $request->ip_address,
					'token'				=> $token,
					'enable'			=> 1,
					'expired_at'		=> $expired_at
				]);

				return response()->json([
					'token'			=> $token,
					'expired_at'		=> $expired_at
				], 200);
			} else {
				return response()->json([
					'message'		=> 'Username atau Password Salah',
					'status'		=> 'Failed'
				], 401);
			}
		}

		return response()->json([
			'message'		=> 'User tidak ditemukan!',
			'status'		=> 'Not Found'
		], 404);
	}
}
