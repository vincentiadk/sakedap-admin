<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

        if ($id) {
            if (session('id')) {
                $whereClause = '';

                if (Main::isNotSuperAdmin()) {
                    $whereClause = 'and branchs.province_id = ' . session('province_id');
                }

                $totalDeliveryMonitoring = QueryAPI::get("
                    select
                        count(case when letter.status in ('TERKIRIM', 'CEK FISIK') then 1 end) as total_verification,
                        count(case when letter.status = 'TERKIRIM' then 1 end) as total_sent,
                        count(case when letter.status in ('DIKIRIM', 'DALAM PENGIRIMAN') then 1 end) as total_in_delivery
                    from
                        letter
                    left join
                        branchs on branchs.id = letter.branch_id
                    where
                        letter.status in ('TERKIRIM', 'CEK FISIK', 'DIKIRIM', 'DALAM PENGIRIMAN')
                        $whereClause
                ", true);

                Config::set('system.total_delivery_verification', $totalDeliveryMonitoring->TOTAL_VERIFICATION ?? 0);
                Config::set('system.total_delivery_sent', $totalDeliveryMonitoring->TOTAL_SENT ?? 0);
                Config::set('system.total_in_delivery', $totalDeliveryMonitoring->TOTAL_IN_DELIVERY ?? 0);
            }

            return $next($request);
        }

        session()->flush();

        return redirect('/');
    }
}
