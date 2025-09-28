<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class SupervisionController extends Controller
{
    public function index($segment)
    {
        $credentialInlis = Main::credentialInlisIFrame();

        if ($segment == 'compliance') {
            $framing = 'https://digitlib.site/Sakedap_Monitoring/DataPenerbit.aspx?l=' . $credentialInlis;
        } else if ($segment == 'coaching') {
            $framing = 'https://digitlib.site/Sakedap_Monitoring/DataJadwalPembinaanList.aspx?l=' . $credentialInlis;
        } else if ($segment == 'monitoring') {
            $framing = 'https://digitlib.site/Sakedap_Monitoring/DataBuktiPemantauanList.aspx?l=' . $credentialInlis;
        } else {
            $framing = '';
        }

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'supervision',
            ]
        ]);
    }
}
