<?php

namespace App\Http\Controllers\PhysicalCollection;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class RetrospectiveCollectionRegistrationController extends Controller
{
    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
         if(Main::isPerpusnas()){
             $framing = 'https://inlis.perpusnas.go.id/inlisnew/KatalogAddDeposit.aspx?l=' . $credentialInlis;
         } else {
            $framing = config('inlis.base_url') . '/KatalogAddDeposit.aspx?l=' . $credentialInlis;
        }
        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'physical-collection.retrospective-collection-registration',
            ]
        ]);
    }
}
