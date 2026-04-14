<?php

namespace App\Http\Middleware;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = session('id');

        if ($id) {
            $maxDays = (int) Cache::remember('setting.password_expired_day', 3600, function () {
                $setting = QueryAPI::get("select * from settingparameters where name = 'PasswordExpiredDay'", true);
                return ($setting->VALUE ?? 60);
            });

            $warningBefore = (int) Cache::remember('setting.password_warning_day', 3600, function () {
                $setting = QueryAPI::get("select * from settingparameters where name = 'PasswordWarningDay'", true);
                return ($setting->VALUE ?? 7);
            });

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

            Config::set('deadline_change_password_text', $notificationText);

            $whereClause = '';

            if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
                $whereClause = 'and branchs.province_id = ' . (int) session('province_id');
            }

            $cacheKey = 'monitoring.delivery.' . (Main::isSuperAdmin() || Main::isPerpusnas() ? 'all' : (int) session('province_id'));

            $totalDeliveryMonitoring = Cache::remember($cacheKey, 120, function () use ($whereClause) {
                return QueryAPI::get("
                    select
                        count(case when letter.status in ('TERKIRIM', 'CEK FISIK') then 1 end) as total_verification,
                        count(case when letter.status = 'TERKIRIM' then 1 end) as total_sent,
                        count(case when letter.status in ('DIKIRIM', 'DALAM PENGIRIMAN') then 1 end) as total_in_delivery
                    from letter
                    left join branchs on branchs.id = letter.branch_id
                    where letter.status in ('TERKIRIM', 'CEK FISIK', 'DIKIRIM', 'DALAM PENGIRIMAN')
                    $whereClause
                ", true);
            });

            Config::set('system.total_delivery_verification', $totalDeliveryMonitoring->TOTAL_VERIFICATION ?? 0);
            Config::set('system.total_delivery_sent', $totalDeliveryMonitoring->TOTAL_SENT ?? 0);
            Config::set('system.total_in_delivery', $totalDeliveryMonitoring->TOTAL_IN_DELIVERY ?? 0);

            return $next($request);
        }

        session()->flush();

        return redirect('/');
    }
}