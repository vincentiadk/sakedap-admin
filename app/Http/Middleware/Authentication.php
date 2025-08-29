<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = session('id');
        $user = QueryAPI::get("select * from users where id = $id", true);

        if ($user) {
            if ($user->ISACTIVE == 1 && $user->ISDELETE == 0) {
                return $next($request);
            }
        }

        session()->flush();

        return redirect('/');
    }
}
