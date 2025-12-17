<?php

return [
    App\Providers\ConfigurationProvider::class,
    App\Providers\AppServiceProvider::class,
    Anhskohbo\NoCaptcha\NoCaptchaServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Milon\Barcode\BarcodeServiceProvider::class,
    ZanySoft\Zip\ZipServiceProvider::class,
];
