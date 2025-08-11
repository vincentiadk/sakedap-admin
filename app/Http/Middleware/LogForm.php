<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogForm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $routeType)
    {
        // Log request details based on route type
        switch ($routeType) {
            case 'register':
                Log::channel('register_log')->info( json_encode($request->all()));
                break;
            case 'login':
                Log::channel('login_log')->info( json_encode($request->all()));
                break;
            case 'forgot_password':
                Log::channel('forgot_password_log')->info( json_encode($request->all()));
                break;
            default:
                // Handle other cases or invalid route types
                break;
        }

        return $next($request);
    }
}
