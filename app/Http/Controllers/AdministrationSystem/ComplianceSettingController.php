<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class ComplianceSettingController extends Controller
{
    private const PARAM_NAMES = [
        'BatasWaktuKonfirmasiTerbitKaryaCetak',
        'BatasWaktuKonfirmasiTerbitDigital',
        'BatasWaktuTeguranKonfirmasiTerbit',
        'BatasMinimumKepatuhanKCKR',
    ];

    private const CACHE_KEY = 'compliance_setting_params';
    private const CACHE_TTL = 3600;

    public function __construct()
    {
        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            abort(403);
        }
    }

    public function index()
    {
        $params = $this->loadParams();

        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.compliance-setting',
                'params'  => $params,
            ]
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'BatasWaktuKonfirmasiTerbitKaryaCetak' => 'required|integer|min:1',
            'BatasWaktuKonfirmasiTerbitDigital'    => 'required|integer|min:1',
            'BatasWaktuTeguranKonfirmasiTerbit'    => 'required|integer|min:1',
            'BatasMinimumKepatuhanKCKR'            => 'required|integer|min:0|max:100',
        ]);

        $currentDate = date('Y-m-d H:i:s');
        $userName    = session('username', 'system');
        $ip          = $request->ip();

        $auditData = [
            'createby'       => $userName,
            'createdate'     => $currentDate,
            'createterminal' => $ip,
            'updateby'       => $userName,
            'updatedate'     => $currentDate,
            'updateterminal' => $ip,
        ];

        $namesList = implode("','", self::PARAM_NAMES);
        $existing  = QueryAPI::get("SELECT * FROM settingparameters WHERE name IN ('$namesList')") ?? [];

        $existingMap = [];
        foreach ($existing as $row) {
            $id   = $row->ID   ?? $row->id   ?? $row->Id   ?? null;
            $name = $row->NAME ?? $row->name ?? $row->Name ?? null;
            if ($id && $name) $existingMap[$name] = (int) $id;
        }

        foreach (self::PARAM_NAMES as $name) {
            $value = $request->input($name);

            if ($value === null || $value === '') {
                continue;
            }

            $payload  = array_merge(['name' => $name, 'value' => $value], $auditData);
            $existing = isset($existingMap[$name]) ? $existingMap[$name] : null;

            if ($existing) {
                unset($payload['createby'], $payload['createdate'], $payload['createterminal']);
                QueryAPI::update('settingparameters', $existingMap[$name], $payload, false);
            } else {
                QueryAPI::create('settingparameters', $payload, false);
            }
        }

        Cache::forget(self::CACHE_KEY);

        return redirect()
            ->route('compliance_setting.index')
            ->with('success', 'Pengaturan kepatuhan KCKR berhasil disimpan.');
    }

    private function loadParams(): array
    {
        $rows = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $namesList = implode("','", self::PARAM_NAMES);
            return QueryAPI::get("SELECT name, value FROM settingparameters WHERE name IN ('$namesList')") ?? [];
        });

        $map = [];
        foreach ($rows as $row) {
            $name  = $row->NAME  ?? $row->name  ?? null;
            $value = $row->VALUE ?? $row->value ?? null;
            if ($name) $map[$name] = $value;
        }

        // Default values jika belum ada di DB
        return [
            'BatasWaktuKonfirmasiTerbitKaryaCetak' => $map['BatasWaktuKonfirmasiTerbitKaryaCetak'] ?? 28,
            'BatasWaktuKonfirmasiTerbitDigital'    => $map['BatasWaktuKonfirmasiTerbitDigital']    ?? 28,
            'BatasWaktuTeguranKonfirmasiTerbit'    => $map['BatasWaktuTeguranKonfirmasiTerbit']    ?? 30,
            'BatasMinimumKepatuhanKCKR'            => $map['BatasMinimumKepatuhanKCKR']            ?? 20,
        ];
    }
}
