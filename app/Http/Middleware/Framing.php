<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Main;
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

        if ($framing) {
            $decode = Main::AESDecrypt($framing);
            parse_str($decode ?? '', $param);

            $segment = $param['segment'] ?? null;
            $username = $param['username'] ?? null;
            $password = isset($param['password']) ? str_replace(' ', '+', $param['password']) : null;

            if ($segment && $username && $password) {
                $decryptedPass = Main::AESDecrypt($password);
                $login = Main::login($username, $decryptedPass);

                if ($login) {
                    return redirect($segment);
                }

                return redirect('/')->with(['failed' => 'Kredensial tidak ditemukan']);
            }
        }

        $response = $next($request);
        $allowedDomain = config('system.iframe_domain');

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' {$allowedDomain}");

        return $response;
    }
}
