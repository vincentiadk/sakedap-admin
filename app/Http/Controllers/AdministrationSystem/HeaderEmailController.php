<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeaderEmailController extends Controller
{
    public function index(Request $request)
    {
        $provinceId = session('province_id');

        if (Main::isSuperAdmin() && Main::isPerpusnas()) {
            $whereProvince = 'and province_id is null';
        } else {
            $whereProvince = 'and province_id = ' . $provinceId;
        }

        $template = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug = 'Header'
                $whereProvince
        ", true);

        if ($request->_token == csrf_token()) {
            if ($template) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'province_id' => Main::isSuperAdmin() && Main::isPerpusnas() ? null : $provinceId,
                ]);
            } else {
                $template = QueryAPI::create('e_settings', [
                    'content' => $request->content,
                    'slug' => 'Header',
                    'province_id' => Main::isSuperAdmin() && Main::isPerpusnas() ? null : $provinceId,
                ]);
            }

            $upload = QueryAPI::uploadFile([
                'type' => 'gambar_template',
                'id' => $template->ID ?? null,
                'iszip' => 0,
                'file' => $request->file('file'),
            ]);

            if ($upload) {
                QueryAPI::update('e_settings', ($template->ID ?? null), [
                    'content' => $upload->FileName
                ]);
            }

            return redirect('administration-system/header-email')->with([
                'success' => 'Data berhasil disimpan'
            ]);
        }

        return view('layouts.index', [
            'data' => [
                'template' => $template,
                'content' => 'administration-system.header-email',
                'plugins' => [
                    'fileinput',
                ]
            ]
        ]);
    }
}
