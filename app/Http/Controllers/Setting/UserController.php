<?php

namespace App\Http\Controllers\Setting;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = 'https://digitlib.site/Sakedap_Monitoring/DataPenerbit.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'setting.user',
            ]
        ]);
    }
}
