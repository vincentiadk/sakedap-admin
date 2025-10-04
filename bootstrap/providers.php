<?php

return [
    App\Providers\ConfigurationProvider::class,
    App\Providers\AppServiceProvider::class,
    Anhskohbo\NoCaptcha\NoCaptchaServiceProvider::class,
    App\Providers\ConfigurationProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Milon\Barcode\BarcodeServiceProvider::class,
];
