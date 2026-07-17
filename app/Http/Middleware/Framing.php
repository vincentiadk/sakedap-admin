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
                    session([
                        'id'                 => $user->ID,
                        'username'           => $user->USERNAME,
                        'name'               => $user->FULLNAME,
                        'email'              => $user->EMAILADDRESS,
                        'province_id'        => $user->PROVINCE_ID ?: 31,
                        'province_name'      => $user->NAMAPROPINSI ?: 'DKI Jakarta',
                        'branch_id'          => $user->BRANCH_ID ?: 37,
                        'branch_name'        => $user->NAME_BRANCH ?: 'Perpustakaan Nasional',
                        'role_id'            => $user->ROLE_ID ?: 1,
                        'last_change_password' => $user->LASTCHANGEPASSWORD ?: null,
                    ]);

                    return redirect($segment);
                }

                return redirect('/')->with(['failed' => 'User tidak ditemukan']);
            }
        }

        $response = $next($request);

        $iframeDomain = trim(config('system.iframe_domain', ''));

        if ($iframeDomain === '*') {
            // Boleh di-embed dari domain mana saja
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

        // Fix SameSite cookie agar session bisa dikirim di iframe cross-site
        if ($request->isSecure() && $iframeDomain) {
            foreach ($response->headers->getCookies() as $cookie) {
                $response->headers->setCookie(
                    new \Symfony\Component\HttpFoundation\Cookie(
                        $cookie->getName(),
                        $cookie->getValue(),
                        $cookie->getExpiresTime(),
                        $cookie->getPath(),
                        $cookie->getDomain(),
                        true,        // secure
                        $cookie->isHttpOnly(),
                        false,
                        'None'       // SameSite=None supaya bisa cross-site
                    )
                );
            }
        }

        return $response;
    }
}
