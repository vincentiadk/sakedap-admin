<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        if (!Main::isSuperAdmin()) {
            abort(403);
        }
    }

    public function index()
    {
        $credentialInlis = Main::credentialInlisIFrame();
        $framing = config('inlis.base_url') . '/DataUser.aspx?l=' . $credentialInlis;

        return view('layouts.index', [
            'data' => [
                'framing' => $framing,
                'content' => 'administration-system.user',
            ]
        ]);
    }
}
