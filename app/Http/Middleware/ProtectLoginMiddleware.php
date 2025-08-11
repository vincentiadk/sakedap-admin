<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ProtectLoginMiddleware
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
        $user = User::where('id', session('id'))->first();
        if (!session('id') || !$user || $user->enable == false) {
            session()->flush();
            return redirect('login');
        }

        if (Request::segment(1) == 'admin' && session('userable_type') != 'admins') {
            return redirect()->back();
        } else if (Request::segment(1) == 'publisher' && session('userable_type') != 'publishers') {
            return redirect()->back();
        }

        if (config('app.env') === 'production') {
            if (session('last_login') != $user->last_login) {
                session()->flush();
                return redirect('login');
            }
        }

        return $next($request);
    }
}
