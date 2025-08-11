<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
//use App\Models\Publisher;
use App\Models\AuthClient;

class AuthenticateApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $client_id     = $request->header('Client-ID');
        $client_secret = $request->header('Client-Secret');
        $authable_id   = $request->header('Authable-ID');

        if ($client_id && $client_secret && $authable_id) {
            $client = AuthClient::where('client_id', $client_id)
                ->where('client_secret', $client_secret)
                ->where('authable_id', $authable_id)
                ->first();

            if ($client) {
                $publisher = User::find($authable_id)->publisher;
                if ($publisher->user) {
                    $user_id = $publisher->user->id;
                } else {
                    $user_id = null;
                }

                session(['publisher_id' => $authable_id, 'user_id' => $user_id]);
                return $next($request);
            } else {
                return response()->json('Data tidak ditemukan', 404);
            }
        } else {
            return response()->json('Mohon isi header client_id, client_secret, authable_id', 401);
        }
    }
}
