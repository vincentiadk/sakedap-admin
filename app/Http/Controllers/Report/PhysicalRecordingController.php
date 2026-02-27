<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class PhysicalRecordingController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        if(Main::isPerpusnas()){
            $framing = 'https://inlis.perpusnas.go.id/deposit/Report/rvRegisterDeposit?l=' . $credentialInlis;
        } else {
            $framing = config('inlis.domain') . '/deposit/Report/rvRegisterDeposit?l=' . $credentialInlis;
        }
        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'report.physical-recording',
            ]
        ]);
    }
}
