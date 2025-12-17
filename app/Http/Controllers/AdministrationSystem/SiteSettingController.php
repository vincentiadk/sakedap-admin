<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SiteSettingController extends Controller
{
    private const SITE_SETTING = [
        'EFOIcon',
        'EFOLogo',
        'EFOAlamat',
        'EFOTelpKantor',
        'EFOTelpKC',
        'EFOTelpKR',
        'EFOEmail',
        'EFOYoutubeNasional',
        'EFOInstagramNasional',
        'EFOWebsiteNasional',
        'EFOYoutube',
        'EFOInstagram',
    ];

    private const FIELD_MAPPING = [
        'EFOAlamat' => 'address',
        'EFOTelpKantor' => 'phone_office',
        'EFOTelpKC' => 'phone_printed',
        'EFOTelpKR' => 'phone_recorded',
        'EFOEmail' => 'email',
        'EFOYoutubeNasional' => 'national_youtube',
        'EFOInstagramNasional' => 'national_instagram',
        'EFOWebsiteNasional' => 'national_website',
        'EFOYoutube' => 'youtube',
        'EFOInstagram' => 'instagram',
    ];

    public function index()
    {
        $settingParameterName = $this->buildInClause(self::SITE_SETTING);
        $settingParameter = QueryAPI::get("SELECT * FROM settingparameters WHERE name IN ($settingParameterName)") ?? [];

        return view('layouts.index', [
            'data' => [
                'settingParameter' => collect($settingParameter),
                'content' => 'administration-system.site-setting',
                'plugins' => [
                    'fileinput',
                ]
            ]
        ]);
    }

    public function submitted(Request $request)
    {
        $redirectUrl = url('administration-system/site-setting');

        try {
            $auditData = $this->getAuditData($request);

            $this->upsertSettingParameters($request, $auditData);

            return redirect($redirectUrl)->with('success', 'Pengaturan situs telah disimpan');
        } catch (\Exception $e) {
            Log::error('Site setting update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect($redirectUrl)->with('failed', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getAuditData(Request $request): array
    {
        $currentDate = now()->format('Y-m-d H:i:s');
        $userName = session('username', 'system');
        $ip = $request->ip();

        return [
            'createby' => $userName,
            'createdate' => $currentDate,
            'createterminal' => $ip,
            'updateby' => $userName,
            'updatedate' => $currentDate,
            'updateterminal' => $ip,
        ];
    }

    private function buildSettingParameters(Request $request): array
    {
        $parameters = [];

        foreach (self::FIELD_MAPPING as $name => $field) {
            $parameters[] = [
                'name' => $name,
                'value' => $request->input($field)
            ];
        }

        return $parameters;
    }

    private function buildInClause($values): string
    {
        $quoted = array_map(fn($value) => "'$value'", $values);

        return implode(',', $quoted);
    }

    private function getExistingRecords($names): array
    {
        $namesList = $this->buildInClause($names);
        $records = QueryAPI::get("SELECT * FROM settingparameters WHERE name IN ($namesList)") ?? [];
        $map = [];

        foreach ($records as $record) {
            $id = $record->ID ?? $record->id ?? $record->Id ?? null;
            $name = $record->NAME ?? $record->name ?? $record->Name ?? null;

            if ($id && $name) {
                $map[$name] = (int) $id;
            }
        }

        return $map;
    }

    private function upsertSettingParameters(Request $request, $auditData)
    {
        $parameters = $this->buildSettingParameters($request);
        $names = array_column($parameters, 'name');
        $existingMap = $this->getExistingRecords($names);

        foreach ($parameters as $param) {
            $name = $param['name'];
            $value = $param['value'];

            if ($value === null || $value === '') {
                continue;
            }

            $this->upsertParameter($name, $value, $existingMap, $auditData);
        }

        $this->handleFileUploads($request, $auditData);
    }

    private function upsertParameter($name, $value, $existingMap, $auditData)
    {
        $payload = [
            'name' => $name,
            'value' => $value,
        ];

        if (isset($existingMap[$name])) {
            $payload = array_merge($payload, [
                'updateby' => $auditData['updateby'],
                'updatedate' => $auditData['updatedate'],
                'updateterminal' => $auditData['updateterminal'],
            ]);

            QueryAPI::update('settingparameters', $existingMap[$name], $payload, false);
        } else {
            $payload = array_merge($payload, $auditData);

            QueryAPI::create('settingparameters', $payload, false);
        }
    }

    private function handleFileUploads(Request $request, $auditData)
    {
        $fileRecords = QueryAPI::get("SELECT * FROM settingparameters WHERE name IN ('EFOIcon','EFOLogo')") ?? [];
        $fileMap = [];

        foreach ($fileRecords as $record) {
            $name = $record->NAME ?? $record->name ?? $record->Name ?? null;
            $id = $record->ID ?? $record->id ?? $record->Id ?? null;

            if ($name && $id) {
                $fileMap[$name] = (int) $id;
            }
        }

        if ($request->hasFile('file_icon')) {
            $this->processFileUpload($request, 'file_icon', 'EFOIcon', $fileMap['EFOIcon'] ?? null, $auditData);
        }

        if ($request->hasFile('file_logo')) {
            $this->processFileUpload($request, 'file_logo', 'EFOLogo', $fileMap['EFOLogo'] ?? null, $auditData);
        }
    }

    private function processFileUpload(Request $request, $fileKey, $paramName, $existingId, $auditData)
    {
        if ($existingId) {
            $this->updateExistingFile($request, $fileKey, $$existingId, $auditData);
        } else {
            $this->createNewFile($request, $fileKey, $paramName, $auditData);
        }
    }

    private function updateExistingFile(Request $request, $fileKey, $id, $auditData)
    {
        QueryAPI::removeFile([
            'type' => 'settingparameters',
            'id' => $id
        ]);

        $uploadFile = QueryAPI::uploadFile([
            'type' => 'settingparameters',
            'id' => $id,
            'iszip' => 0,
            'file' => $request->file($fileKey),
        ]);

        if ($uploadFile) {
            QueryAPI::update('settingparameters', $id, [
                'value' => $uploadFile->FileName,
                'updateby' => $auditData['updateby'],
                'updatedate' => $auditData['updatedate'],
                'updateterminal' => $auditData['updateterminal'],
            ], false);
        }
    }

    private function createNewFile(Request $request, $fileKey, $paramName, $auditData)
    {
        $createData = QueryAPI::create('settingparameters', [
            'name' => $paramName,
            'createby' => $auditData['createby'],
            'createdate' => $auditData['createdate'],
            'createterminal' => $auditData['createterminal'],
            'updateby' => $auditData['updateby'],
            'updatedate' => $auditData['updatedate'],
            'updateterminal' => $auditData['updateterminal'],
        ], false);

        if ($createData) {
            $newId = $createData->ID ?? $createData->id ?? $createData->Id ?? null;

            if ($newId) {
                $uploadFile = QueryAPI::uploadFile([
                    'type' => 'setting_parameter',
                    'id' => $newId,
                    'iszip' => 0,
                    'file' => $request->file($fileKey),
                ]);

                if ($uploadFile) {
                    QueryAPI::update('settingparameters', $newId, [
                        'value' => $uploadFile->FileName,
                    ], false);
                }
            }
        }
    }
}
