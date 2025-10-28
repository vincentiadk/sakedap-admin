<?php

namespace App\Http\Controllers\PhysicalCollection;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class RetrospectiveCollectionRegistrationController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.base_url') . '/KatalogAddDeposit.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'physical-collection.retrospective-collection-registration',
            ]
        ]);
    }
}
