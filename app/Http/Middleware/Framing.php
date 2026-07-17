<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Main;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class Framing
{
    public function handle(Request $request, Closure $next): Response
    {
        $framing = str_replace(' ', '+', $request->framing);

        if ($framing) {
            $decode = Main::AESDecrypt($framing);
            parse_str($decode ?? '', $param);

            $segment  = $param['segment']  ?? null;
            $username = $param['username'] ?? null;

            if ($segment && $username) {
                $username = str_replace("'", "''", $username);
                $user = \App\Helpers\QueryAPI::get("
                    SELECT
                        users.*,
                        branchs.province_id AS province_id,
                        branchs.name AS name_branch,
                        propinsi.namapropinsi AS namapropinsi
                    FROM users
                    LEFT JOIN branchs ON branchs.id = users.branch_id
                    LEFT JOIN propinsi ON propinsi.id = branchs.province_id
                    WHERE LOWER(users.username) = LOWER('$username')
                      AND (users.isdelete = 0 OR users.isdelete IS NULL)
                ", true);

                if ($user) {
                    $request->session()->put([
                        'id'                   => $user->ID,
                        'username'             => $user->USERNAME,
                        'name'                 => $user->FULLNAME,
                        'email'                => $user->EMAILADDRESS,
                        'province_id'          => $user->PROVINCE_ID ?: 31,
                        'province_name'        => $user->NAMAPROPINSI ?: 'DKI Jakarta',
                        'branch_id'            => $user->BRANCH_ID ?: 37,
                        'branch_name'          => $user->NAME_BRANCH ?: 'Perpustakaan Nasional',
                        'role_id'              => $user->ROLE_ID ?: 1,
                        'last_change_password' => $user->LASTCHANGEPASSWORD ?: null,
                    ]);
                    $request->session()->save();

                    $response = redirect($segment);
                    return $this->applySameSite($request, $response);
                }

                return redirect('/')->with(['failed' => 'User tidak ditemukan']);
            }
        }

        $response = $next($request);

        $iframeDomain = trim(config('system.iframe_domain', ''));

        if ($iframeDomain === '*') {
            $response->headers->remove('X-Frame-Options');
            $response->headers->set('Content-Security-Policy', "frame-ancestors *");
        } else {
            $domains      = array_filter(explode(' ', $iframeDomain));
            $frameOrigins = implode(' ', $domains);
            $csp          = $frameOrigins ? "frame-ancestors 'self' {$frameOrigins}" : "frame-ancestors 'self'";
            if ($frameOrigins) {
                $response->headers->remove('X-Frame-Options');
            } else {
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            }
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $this->applySameSite($request, $response);
    }

    private function applySameSite(Request $request, Response $response): Response
    {
        $iframeDomain = trim(config('system.iframe_domain', ''));

        if ($request->isSecure() && $iframeDomain) {
            foreach ($response->headers->getCookies() as $cookie) {
                $response->headers->setCookie(
                    new Cookie(
                        $cookie->getName(),
                        $cookie->getValue(),
                        $cookie->getExpiresTime(),
                        $cookie->getPath(),
                        $cookie->getDomain(),
                        true,
                        $cookie->isHttpOnly(),
                        false,
                        'None'
                    )
                );
            }
        }

        return $response;
    }
}
