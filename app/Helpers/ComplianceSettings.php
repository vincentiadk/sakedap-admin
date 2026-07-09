<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class ComplianceSettings
{
    private const CACHE_KEY = 'compliance_setting_params';
    private const CACHE_TTL = 3600;

    private const DEFAULTS = [
        'BatasWaktuKonfirmasiTerbitKaryaCetak' => 30,
        'BatasWaktuKonfirmasiTerbitDigital'    => 15,
        'BatasWaktuTeguranKonfirmasiTerbit'    => 30,
        'BatasMinimumKepatuhanKCKR'            => 20,
    ];

    private const PARAM_NAMES = [
        'BatasWaktuKonfirmasiTerbitKaryaCetak',
        'BatasWaktuKonfirmasiTerbitDigital',
        'BatasWaktuTeguranKonfirmasiTerbit',
        'BatasMinimumKepatuhanKCKR',
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
        $rows = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
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
            $result[$key] = isset($map[$key]) ? (int) $map[$key] : $default;
        }

        return $result;
    }
}
