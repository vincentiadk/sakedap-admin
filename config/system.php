<?php

return [
    'retry_login' => 3,
    'retry_login_interval' => 1,
    'aes_key' => null,
    'aes_iv' => null,
    'iframe_domain' => null,
    'limit_reset_password' => 2,
    'limit_file_original' => 1,
    'catalog_cover_max_upload' => 2048,
    'catalog_content_max_upload' => 204800,
    'limit_submission_kckr' => 3,
    'limit_grant' => 3,
    'limit_retur' => 3,
    'time_printed_work' => 90,
    'time_recording_work' => 365,
    'executor_start_date' => date('Y-m-d'),
    'max_coaching' => 10,
    'fo_url' => env('FO_URL'),
];
