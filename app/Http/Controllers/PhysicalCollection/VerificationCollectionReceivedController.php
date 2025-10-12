<?php

namespace App\Http\Controllers\PhysicalCollection;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class VerificationCollectionReceivedController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = 'https://digitlib.site/inlis-ent-2025/KoleksiUnverifiedDeposit.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'physical-collection.verification-collection-received',
            ]
        ]);
    }
}
