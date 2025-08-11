<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\DesktopToken;

class DesktopTokenValidation
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
        $authorization = $request->header('Authorization');
        $ip_address = $request->header('IpAddress');

        if (!$authorization) {
            return response()->json([
                'message'       => 'Token is required!',
                'status'        => 'Failed'
            ], 401);
        }

        $dekstopToken = DesktopToken::where('token', $authorization)->first();

        if (!$dekstopToken) {
            return response()->json([
                'message'       => 'Token is required!',
                'status'        => 'Failed'
            ], 401);
        }

        if ($ip_address != $dekstopToken->ip_address) {
            return response()->json([
                'message'       => 'IP Address not valid.',
                'status'        => 'Failed'
            ], 401);
        }
        if ($dekstopToken->enable == 0) {
            return response()->json([
                'message'       => 'Desktop application is disabled. Contact Administrator',
                'status'        => 'Failed'
            ], 401);
        }
        if (strtotime(date('Y-m-d H:i:s')) > strtotime($dekstopToken->expired_at)) {
            return response()->json([
                'message'       => 'Token expired.',
                'status'        => 'Failed'
            ], 401);
        }


        return $next($request);
    }
}
