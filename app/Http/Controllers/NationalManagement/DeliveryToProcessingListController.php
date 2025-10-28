<?php

namespace App\Http\Controllers\NationalManagement;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class DeliveryToProcessingListController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.base_url') . '/DataPengirimanBahanPustakaUserPengolahan.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'national-management.delivery-to-processing-list',
            ]
        ]);
    }
}
