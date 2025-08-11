<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu;

class ProtectMenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $url)
    {
        $data = Menu::where('url', $url)
            ->whereHas('userAccess', function ($query) {
                $query->where('role_id', session('role_id'));
            })
            ->count();

        if ($data < 1) {
            return abort(401);
        }

        return $next($request);
    }
}
