<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmailLinkResetPassword;
use App\Jobs\SendEmailSuccessResetPassword;
use DB;
use Illuminate\Support\Str;
use App\Models\Publisher;
use App\Models\User;
use App\Models\Setting;
use Validator;

class ResetPasswordController extends Controller
{
	public function resetPassword(Request $request)
	{
		if ($request->has('_token')) {

			$user = DB::table('users')->where('email', $request->email)->first();
			//Check if the user exists
			if (!$user) {
				return redirect()->back()->with('failed', 'Email tidak ditemukan');
			}

			//Create Password Reset Token
			DB::table('password_resets')->insert([
				'email' => $request->email,
				'token' => Str::random(60),
				'created_at' => date('Y-m-d H:i:s'),
				'expired_at' => date('Y-m-d H:i:s', strtotime(' +2 hours '))
			]);

			//Get the token just created above
			$tokenData = DB::table('password_resets')
				->where('email', $request->email)
				->orderBy('id', 'desc')
				->first();

			try {

				$link = url('reset-password/confirm/' . $tokenData->token . '?email=' . urlencode($user->email));

				$publisher = DB::table('publishers')->where('id', $user->userable_id)->first();

				$params = [
					'name'   	=> $publisher->name,
					'email'		=> $user->email,
					'link'      => $link
				];

				$job = new SendEmailLinkResetPassword($params);
				dispatch(($job)->onQueue('notification'));

				return redirect()->back()->with('success', 'Link reset password telah dikirim ke Email Anda');
			} catch (\Exception $e) {
				return redirect()->back()->with('failed', 'Server Error');
			}
		} else {
			return view('reset_password');
		}
	}

	public function confirmResetPassword(Request $request, $token)
	{
		if ($request->has('_token')) {

			$validator = Validator::make($request->all(), [
				'email' 	=> 'required|email|exists:users,email',
				'password' 	=> 'required'
			]);

			//check if payload is valid before moving on
			if ($validator->fails()) {
				return redirect()->back()->with('failed', 'Mohon lengkapi data');
			}

			$tokenData = DB::table('password_resets')
				->where('token', $token)
				->where('email', $request->email)
				->orderBy('id', 'desc')
				->first();

			$current_time = strtotime(date('Y-m-d H:i'));
			$schedule_time = strtotime(date('Y-m-d H:i', strtotime($tokenData->expired_at)));

			$diff = $schedule_time - $current_time;
			$minutes = floor($diff / 60);

			if ($minutes < 0) {
				return redirect()->back()->with('failed', 'Token kadaluarsa');
			}

			$user = User::where('email', $request->email)->first();
			//Check if the user exists
			if (!$user) {
				return redirect()->back()->with('failed', 'Email tidak ditemukan');
			}

			$user->update([
				'password'	=> \Hash::make($request->password)
			]);

			DB::table('password_resets')
				->where('token', $token)
				->where('email', $request->email)->delete();

			$publisher = DB::table('publishers')->where('id', $user->userable_id)->first();

			$header      = Setting::where('slug', 'template-email-header')->first();
			$footer      = Setting::where('slug', 'template-email-footer')->first();
			$link_header = public_path('storage/' . str_replace('public/', '', $header->content));
			$link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));

			$params = [
				'name'   	=> $publisher->name,
				'email'		=> $user->email,
				'header'      => '<img src="' . $link_header . '" style="width:100%;">',
				'footer'      => '<img src="' . $link_footer . '" style="width:100%;">',
			];

			$job = new SendEmailSuccessResetPassword($params);
			dispatch(($job)->onQueue('notification'));

			return redirect('login')->with('success', 'Password Berhasil diganti');
		} else {
			return view('change_password');
		}
	}
}
