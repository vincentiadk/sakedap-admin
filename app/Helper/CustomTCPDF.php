<?php

namespace App\Helper;

use \TCPDF;
use App\Models\Setting;
use Storage;

class CustomTCPDF extends TCPDF
{

    //Page header
    public function Header()
    {

        $templateHeader = Setting::where('slug', 'template-email-header')->first();
        $this->Image(Storage::disk('local')->path($templateHeader->content), 10, 10, 190, 30, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }

    // // Page footer
    public function Footer()
    {

        $templateHeader = Setting::where('slug', 'template-email-footer')->first();

        // Position at 15 mm from bottom
        $this->Image(Storage::disk('local')->path($templateHeader->content), 13, 265, 182, 23, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}
