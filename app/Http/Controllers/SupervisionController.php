<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use App\Http\Controllers\Controller;

class SupervisionController extends Controller
{
    public function index($segment)
    {
        $userId = session('id');
        $encFrameInlis = Main::AESCrypt(
            "userid=$userId;auth=1",
            base64_decode(env('AES_KEY_INLIS')),
            base64_decode(env('AES_IV_INLIS'))
        );

        if ($segment == 'compliance') {
            $framing = 'https://digitlib.site/Sakedap_Monitoring/DataPenerbit.aspx?l=' . $encFrameInlis;
        } else if ($segment == 'coaching') {
            $framing = 'https://digitlib.site/Sakedap_Monitoring/DataJadwalPembinaanList.aspx?l=' . $encFrameInlis;
        } else if ($segment == 'monitoring') {
            $framing = 'https://digitlib.site/Sakedap_Monitoring/DataBuktiPemantauanList.aspx?l=' . $encFrameInlis;
        } else {
            $framing = '';
        }

        $data = [
            'framing' => $framing,
            'content' => 'supervision'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
