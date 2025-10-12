<?php

namespace App\Http\Controllers\NationalManagement;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class AlignmentStorageController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = 'https://digitlib.site/inlis-ent-2025/KoleksiListDepositPenjajaran.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'national-management.alignment-storage',
            ]
        ]);
    }
}
