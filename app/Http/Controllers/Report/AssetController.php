<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class AssetController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        if(Main::isPerpusnas()){
            $framing = 'https://inlis.perpusnas.go.id/deposit/Report/rvAsset?l=' . $credentialInlis;
        } else {
            $framing = config('inlis.inlis_url') . '/deposit/Report/rvAsset?l=' . $credentialInlis;
        }
        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'report.asset',
            ]
        ]);
    }
}
