<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Solarium\Client;
use DB;
use Illuminate\Support\Str;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;


class Indexer
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(new Curl(), new EventDispatcher(), config('solr'));
    }

    public function searchById($table_name, $system_name, $table_id)
    {
        try {
            $query = $this->client->createSelect();

            if($table_name) $query->createFilterQuery('table_name')->setQuery("table_name:" . $table_name);
            if($system_name) $query->createFilterQuery('system_name')->setQuery("system_name:" . $system_name);
            $query->createFilterQuery('table_id')->setQuery("table_id:" . $table_id);

            $resultset = $this->client->execute($query);
            $return = $this->getResult($table_name, $resultset);

            return $return;
        } catch (\Solarium\Exception\HttpException $e) {
            return [];
        }
        
    }

    public function getResult($table_name, $resultset)
    {
        $model = new Collection;
        switch ($table_name) {
            case "collections":
                foreach ($resultset as $document) {
                    $array = [];

                    switch ($document['system_name']) {
                        case "edeposit":
                            $array = $this->getDataEdeposit($document);
                            break;
                    }

                    $model->push($array);
                }
                break;
            default:
                break;
        }
        return $model;
    }

     private function getDataEdeposit($item) {

        return [
            'id' => $item['id'],
            'table_id' => $item['table_id'],
            'system_name' => $item['system_name'],
            'table_name' => $item['table_name'],
            'title' => $item['title'] ? $item['title'][0] : '',
            'copyright' => isset($item['rights']) ? $item['rights'][0] : '',
            'deposit' => $item['deposit'],
            'province_name' => $item['province_name'],
            'city_name' => $item['city_name'],
            'ddc' => isset($item['ddc']) ? $item['ddc'][0] : '',
            'type' => isset($item['type']) ? $item['type'][0] : '',
            'jenis' => $item['jenis'],
            'sub_title' =>  '',
            'contributor' => $item['creator'],
            "description_total" => isset($item['description_total']) ? $item['description_total'][0] : '',       
            'description_other' => isset($item['description_other']) ? $item['description_other'][0] : '',       
            'description_dimension' => isset($item['description_dimension']) ? $item['description_dimension'][0] : '',       
            'subject' => $item['subject'],       
            'publication_place' => $item['publication_place'],        
            'publisher_name' => isset($item['publisher']) ? $item['publisher'][0] : '',      
            'publication_year' => isset($item['year_string']) ? $item['year_string'][0] : '',       
            'code' => isset($item['code']) ? $item['code'][0] : '',            
            'cover' => isset($item['cover']) ? $item['cover'] : '',
            'file' => isset($item['link_original']) ? $item['link_original'][0] : '',      
            'abstract' => isset($item['description']) ? $item['description'][0] : '',   
            'jenis_karya' => 'Karya Rekam', 
            'created_at' => date('Y-m-d h:m:s ', strtotime($item['created_at'])),    
            'updated_at' => date('Y-m-d h:m:s', strtotime($item['updated_at'])),    
            'deleted_at' => date('Y-m-d h:m:s', strtotime($item['deleted_at'])),    
            'received_date' => date('Y-m-d h:m:s', strtotime($item['received_date'])),    
        ];
    }
    
}
