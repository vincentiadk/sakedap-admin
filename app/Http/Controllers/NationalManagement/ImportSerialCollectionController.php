<?php

namespace App\Http\Controllers\NationalManagement;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class ImportSerialCollectionController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.base_url') . '/KoleksiImportSerialDeposit.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'national-management.import-serial-collection',
            ]
        ]);
    }
}
