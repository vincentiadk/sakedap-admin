<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class AssetController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.domain') . '/deposit/Report/rvAsset?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'report.asset',
            ]
        ]);
    }
}
