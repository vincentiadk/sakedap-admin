<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class RecordingController extends Controller
{
    public function index($segment)
    {
        $credentialInlis = Main::credentialInlisIFrame();

        if ($segment == 'accept') {
            $framing = 'https://digitlib.site/inlis-ent-2025/KoleksiUnverifiedDeposit.aspx?l=' . $credentialInlis;
        } else if ($segment == 'deposit') {
            $framing = 'https://digitlib.site/inlis-ent-2025/KoleksiListDeposit.aspx?l=' . $credentialInlis;
        } else if ($segment == 'registration') {
            $framing = 'https://digitlib.site/inlis-ent-2025/KatalogAddDeposit.aspx?l=' . $credentialInlis;
        } else if ($segment == 'catalog') {
            $framing = 'https://digitlib.site/inlis-ent-2025/KatalogList.aspx?l=' . $credentialInlis;
        } else {
            $framing = '';
        }

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'recording',
            ]
        ]);
    }
}
