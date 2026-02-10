<?php

namespace App\Http\Controllers\CoachingSupervision;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class MonitoringController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.domain') . '/DataBuktiPemantauanList.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'coaching-supervision.monitoring',
            ]
        ]);
    }
}
