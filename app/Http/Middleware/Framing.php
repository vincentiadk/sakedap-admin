<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Framing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $framing = str_replace(' ', '+', $request->framing);
        $response = $next($request);
        $allowedDomain = env('ALLOW_IFRAME_DOMAIN');
        $cspHeader = "frame-ancestors 'self' {$allowedDomain}";

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', $cspHeader);

        if ($framing) {
            $decode = Main::AESDecrypt($framing);
            $param = [];

            parse_str($decode ?? '', $param);

            $segment = isset($param['segment']) ? $param['segment'] : null;
            $username = isset($param['username']) ? $param['username'] : null;
            $password = isset($param['password']) ? str_replace(' ', '+', $param['password']) : null;

            if ($segment && $username && $password) {
                $login = Main::login($username, Main::AESDecrypt($password));

                if ($login) {
                    return redirect($segment ?? '/');
                }

                return redirect('/')->with(['failed' => 'Kredensial tidak ditemukan']);
            }
        }

        return $response;
    }
}
