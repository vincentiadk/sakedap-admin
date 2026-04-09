<?php

namespace App\Http\Middleware;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Carbon\Carbon;
use Closure;
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
                $maxDays = QueryAPI::get("select * from setting_parameters where name = 'PasswordExpiredDay'", true)->VALUE ?? 60;
                $warningBefore = QueryAPI::get("select * from setting_parameters where name = 'PasswordWarningDay'", true)->VALUE ?? 7;
                $lastChangeSession = session('last_change_password');

                $notificationText = null;

                if ($lastChangeSession) {
                    $lastChange = Carbon::parse($lastChangeSession);
                    $expiryDate = $lastChange->copy()->addDays($maxDays);
                    $daysRemaining = Carbon::now()->startOfDay()->diffInDays($expiryDate, false);

                    if ($daysRemaining == 0) {
                        $notificationText = 'Hari ini adalah hari terakhir untuk memperbarui password Anda.';
                    } elseif ($daysRemaining < 0) {
                        $notificationText = 'Masa berlaku password Anda telah habis ' . $expiryDate->diffForHumans() . '. Segera ubah!';
                    } elseif ($daysRemaining <= $warningBefore) {
                        $notificationText = 'Password Anda akan kedaluwarsa ' . $expiryDate->diffForHumans();
                    }
                }

                config(['deadline_change_password_text' => $notificationText]);

                $whereClause = '';

                if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
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
