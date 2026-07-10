<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class ComplianceSettings
{
    private const CACHE_TTL = 3600;

    private static function cacheKey(): string
    {
        return 'compliance_setting_params_' . substr(md5(implode(',', self::PARAM_NAMES)), 0, 8);
    }

    private const DEFAULTS = [
        'BatasWaktuKonfirmasiTerbitKaryaCetak' => 30,
        'BatasWaktuKonfirmasiTerbitDigital'    => 15,
        'BatasWaktuTeguranKonfirmasiTerbit'    => 30,
        'BatasMinimumKepatuhanKCKR'            => 20,
        'AutoBlokir_Aktif'                     => '0',
        'AutoBlokir_MulaiTanggal'              => null,
        'RedaksiKonfirmasiSSKCKR'              => null,
        'RedaksiKonfirmasiTanggalTerbitISBN'   => null,
    ];

    private const PARAM_NAMES = [
        'BatasWaktuKonfirmasiTerbitKaryaCetak',
        'BatasWaktuKonfirmasiTerbitDigital',
        'BatasWaktuTeguranKonfirmasiTerbit',
        'BatasMinimumKepatuhanKCKR',
        'AutoBlokir_Aktif',
        'AutoBlokir_MulaiTanggal',
        'RedaksiKonfirmasiSSKCKR',
        'RedaksiKonfirmasiTanggalTerbitISBN',
    ];

    /**
     * Short hash of current settings values — use this in other cache keys
     * so they auto-invalidate when settings change.
     */
    public static function cacheVersion(): string
    {
        return substr(md5(serialize(self::get())), 0, 8);
    }

    /**
     * Load compliance settings from cache/DB.
     * Returns array with 4 integer keys.
     */
    public static function get(): array
    {
        $rows = Cache::remember(self::cacheKey(), self::CACHE_TTL, function () {
            $namesList = implode("','", self::PARAM_NAMES);
            return QueryAPI::get("SELECT name, value FROM settingparameters WHERE name IN ('$namesList')") ?? [];
        });

        $map = [];
        foreach ($rows as $row) {
            $name  = $row->NAME  ?? $row->name  ?? null;
            $value = $row->VALUE ?? $row->value ?? null;
            if ($name !== null) $map[$name] = $value;
        }

        $result = [];
        foreach (self::DEFAULTS as $key => $default) {
            if (in_array($key, ['AutoBlokir_Aktif', 'AutoBlokir_MulaiTanggal', 'RedaksiKonfirmasiSSKCKR', 'RedaksiKonfirmasiTanggalTerbitISBN'])) {
                $result[$key] = $map[$key] ?? $default;
            } else {
                $result[$key] = isset($map[$key]) ? (int) $map[$key] : $default;
            }
        }

        return $result;
    }
}
