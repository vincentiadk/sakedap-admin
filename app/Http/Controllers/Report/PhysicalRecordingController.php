<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class PhysicalRecordingController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = 'https://digitlib.site/inlis-ent-2025/_eksternal.aspx?url=/deposit/Report/rvRegisterDeposit?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'report.physical-recording',
            ]
        ]);
    }
}
