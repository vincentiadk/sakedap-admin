<?php

namespace App\Helper;

use Solarium\Core\Client\Adapter\Curl;

class SolrAdapter extends Curl
{
    public function createHandle($request, $endpoint)
    {
        $handle = parent::createHandle($request, $endpoint);

        // add your own options to the cURL handle
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);

        return $handle;
    }
}
