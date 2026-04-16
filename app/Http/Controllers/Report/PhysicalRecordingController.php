<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class PhysicalRecordingController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.inlis_url') . '/deposit/Report/rvRegisterDeposit?l=' . $credentialInlis;
        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'report.physical-recording',
            ]
        ]);
    }
}
