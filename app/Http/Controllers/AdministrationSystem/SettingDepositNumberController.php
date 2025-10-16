<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class SettingDepositNumberController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = 'https://digitlib.site/inlis-ent-2025/SettingParameterDeposit.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'administration-system.setting-deposit-number',
            ]
        ]);
    }
}
