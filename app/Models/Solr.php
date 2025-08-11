<?php

namespace App\Models;

use Solarium\Client;
use App\Helper\SolrAdapter;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use Solarium\Core\Client\Adapter\Curl;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Solr extends Model
{

    public static function solrClient($core)
    {
        // dd(env('SOLR_HOST', 'localhost'));
        if (env('SOLR_HOST', 'localhost') == 'solr-kckr.perpusnas.go.id') {
            $adapter = new SolrAdapter();
        } else {
            $adapter = new Curl();
        }


        return new Client($adapter, new EventDispatcher(), config('solr.' . $core));
    }

    public static function data($core, $table, $data = [], $pagination = [], $countable = false)
    {
        $result = [];
        $client = self::solrClient($core);

        $solr_query = $client->createSelect();
        $solr_query->createFilterQuery($table)->setQuery('table_name:' . $table);

        if (array_key_exists('id', $data) && $core == 'isbn') {
            $solr_query->createFilterQuery('table_id')->setQuery('table_id:' . $data['id']);
        } else {
            foreach ($data as $key => $d) {
                $solr_query->createFilterQuery($key)->setQuery($key . ':' . $d);
            }
        }

        if ($pagination) {
            $solr_query->setStart($pagination['offset'])->setRows($pagination['limit']);
        } else {
            $solr_query->setStart(0)->setRows(10000);
        }

        $result_solr = $client->execute($solr_query);
        if ($countable) {
            $result = $result_solr->getNumFound();
        } else {
            foreach ($result_solr as $rs) {
                $result[] = $rs;
            }
        }

        return $result;
    }

    // public static function dataFacet($core, $table, $data = [], $pagination = [], $countable = false, $facet = [])
    // {
    //     $result = [];
    //     $client = self::solrClient($core);

    //     $solr_query = $client->createSelect();
    //     $solr_query->createFilterQuery($table)->setQuery('table_name:' . $table);

    //     if (array_key_exists('id', $data) && $core == 'isbn') {
    //         $solr_query->createFilterQuery('table_id')->setQuery('table_id:' . $data['id']);
    //     } else {
    //         foreach ($data as $key => $d) {
    //             $solr_query->createFilterQuery($key)->setQuery($key . ':' . $d);
    //         }
    //     }

    //     if ($pagination) {
    //         $solr_query->setStart($pagination['offset'])->setRows($pagination['limit']);
    //     } else {
    //         $solr_query->setStart(0)->setRows(10000);
    //     }

    //     // Configure facet parameters
    //     if ($facet) {

    //         if ($countable) {
    //             $result_solr = $client->execute($solr_query);
    //             $result = $result_solr->getNumFound();
    //         } else {
    //             // get the facetset component
    //             $facetSet = $solr_query->getFacetSet();

    //             // create a facet field instance and set options
    //             $facetSet->createFacetField($facet['key'])->setField($facet['value']);

    //             // this executes the query and returns the result
    //             $resultset = $client->execute($solr_query);

    //             dd($resultset);

    //             $resultFacet = $resultset->getFacetSet()->getFacet($facet['key']);
    //             foreach ($resultFacet as $key => $count) {
    //                 $result[$key] = $count;
    //             }
    //         }
    //     } else {
    //         $result_solr = $client->execute($solr_query);
    //         if ($countable) {
    //             $result = $result_solr->getNumFound();
    //         } else {
    //             foreach ($result_solr as $rs) {
    //                 $result[] = $rs;
    //             }
    //         }
    //     }

    //     return $result;
    // }


    public static function postUpdate($core, $table, $param, $data)
    {
        $row = self::data($core, $table, $param);
        if (count($row) > 0) {
            $client   = self::solrClient($core);
            $solr     = $client->createUpdate();
            $buffer   = $client->getPlugin('bufferedadd');

            $buffer->setBufferSize(50000);
            $buffer->flush();
            $solr_doc = $solr->createDocument();

            $solr_doc->setKey('id', $row[0]['id']);
            foreach ($data as $column => $value) {
                $solr_doc->setField($column, $value, null, $solr_doc::MODIFIER_SET);
            }

            $solr->addDocuments([$solr_doc]);
            $solr->addCommit();
            $client->update($solr);

            return true;
        }

        return false;
    }
    public static function summaryBillIsbn($core, $kd_penerbit, $request)
    {
        $total_bill = self::data($core, 'complete', [], [], true);
        $client     = self::solrClient($core);
        $solr_query = $client->createSelect();
        $solr_query->createFilterQuery('complete')->setQuery('table_name:complete');
        $solr_query->createFilterQuery('kd_penerbit')->setQuery('kd_penerbit:' . $kd_penerbit);

        $summary = [
            'percentage'       => 0,
            'request_elek'     => 0,
            'received_elek'    => 0,
            'total_bill_elek'  => 0,
            'request_cetak'    => 0,
            'received_cetak'   => 0,
            'total_bill_cetak' => 0,
            'total_all_bill'   => self::data($core, 'complete', ['kd_penerbit' => $kd_penerbit], [], true),
            'total_all_rest'   => self::data($core, 'complete', ['kd_penerbit' => $kd_penerbit, 'received_date' => '[* TO *]'], [], true)
        ];


        if ($request["param"]) {
            if ($request["param"] == 'annual') {
                $start  = $request["year_start"] . '-01-01T00:00:00Z';
                $finish = $request["year_end"] . '-12-31T23:59:59Z';
            } else if ($request["param"] == 'monthly') {
                $start  = $request["month_year_start"] . '-' . $request["month_start"] . '-01T00:00:00Z';
                $finish = date('Y-m-t', strtotime($request["month_year_end"] . '-' . $request["month_end"])) . 'T23:59:59Z';
            } else if ($request["param"] == 'daily') {
                $start  = $request["day_start"] . 'T00:00:00Z';
                $finish = $request["day_end"] . 'T23:59:59Z';
            }
            $solr_query->createFilterQuery('created_date')->setQuery("created_date:[$start TO $finish]");
        }

        $solr_query->setStart(0)->setRows($total_bill);
        $result_solr = $client->execute($solr_query);

        foreach ($result_solr as $rs) {
            if ($rs['jenis'] == 'elek') {
                $summary['total_bill_elek'] += 1;
                if (!isset($rs['received_date'])) {
                    $summary['request_elek'] += 1;
                } else {
                    $summary['received_elek'] += 1;
                }
            } else {
                $summary['total_bill_cetak'] += 1;
                if (!isset($rs['received_date'])) {
                    $summary['request_cetak'] += 1;
                } else {
                    $summary['received_cetak'] += 1;
                }
            }
        }

        $collect    = $summary['received_elek'] + $summary['received_cetak'];
        $total_bill = $summary['total_bill_elek'] + $summary['total_bill_cetak'];

        if ($collect == 0 & $total_bill == 0) {
            $summary['percentage'] = '-';
        } else {
            $summary['percentage'] = ceil(($collect / $total_bill) * 100);
        }

        return $summary;
    }
    public static function summaryBillIsbnNew($core, $kd_penerbit, $request)
    {
        $dateNow = getDate();
        $param_date = "created_date:[" . $dateNow["year"] . '-01-01T00:00:00Z TO ' . $dateNow["year"] . '-12-31T23:59:59Z]';
        if ($request["param"]) {
            if ($request["param"] == 'annual') {
                $start  = $request["year_start"] . '-01-01T00:00:00Z';
                $finish = $request["year_end"] . '-12-31T23:59:59Z';
            } else if ($request["param"] == 'monthly') {
                $start  = $request["month_year_start"] . '-' . $request["month_start"] . '-01T00:00:00Z';
                $finish = date('Y-m-t', strtotime($request["month_year_end"] . '-' . $request["month_end"])) . 'T23:59:59Z';
            } else if ($request["param"] == 'daily') {
                $start  = $request["day_start"] . 'T00:00:00Z';
                $finish = $request["day_end"] . 'T23:59:59Z';
            }
            $param_date = $request['type_date'] . ":" . "[$start TO $finish]";
        }
        $summary = [
            'percentage'       => 0,
            'request_elek'     => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:elek AND ' . $param_date),
            'received_elek'    => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:elek AND received_date:[* TO *] AND ' . $param_date),
            'total_bill_elek'  => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:elek AND -received_date:[* TO *] AND ' . $param_date),
            'request_cetak'    => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:cetak AND ' . $param_date),
            'received_cetak'   => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:cetak AND received_date:[* TO *] AND ' . $param_date),
            'total_bill_cetak' => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:cetak AND -received_date:[* TO *] AND ' . $param_date),
            'total_all_bill'   => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND -received_date:[* TO *] AND ' . $param_date),
            'total_all_rest'   => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND received_date:[* TO *]' . ' AND ' . $param_date)
        ];


        $collect    = $summary['received_elek'] + $summary['received_cetak'];
        $total_request = $summary['request_cetak'] + $summary['request_elek'];

        if ($total_request == 0) {
            $summary['percentage'] = '-';
        } else {
            $summary['percentage'] = ceil(($collect / $total_request) * 100);
        }

        return $summary;
    }

    public static function summaryBillIsbnFrontend($core, $kd_penerbit, $request)
    {
        $dateNow = getDate();
        $param_date = "created_date:[" . $dateNow["year"] . '-01-01T00:00:00Z TO ' . $dateNow["year"] . '-12-31T23:59:59Z]';
        if (!empty($request["year_start"]) && !empty($request["year_end"])) {
            $start  = $request["year_start"] . '-01-01T00:00:00Z';
            $finish = $request["year_end"] . '-12-31T23:59:59Z';
            $param_date = "created_date:[$start TO $finish]";
        }

        $summary = [
            'percentage'       => 0,
            'request_elek'     => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:elek AND ' . $param_date),
            'received_elek'    => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:elek AND received_date:[* TO *] AND ' . $param_date),
            'request_cetak'    => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:cetak AND ' . $param_date),
            'received_cetak'   => self::getSolrNumFound($core, 'table_name:complete AND kd_penerbit:' . $kd_penerbit . ' AND jenis:cetak AND received_date:[* TO *] AND ' . $param_date),
        ];


        $collect    = $summary['received_elek'] + $summary['received_cetak'];
        $total_request = $summary['request_cetak'] + $summary['request_elek'];

        if ($total_request == 0) {
            $summary['percentage'] = '-';
        } else {
            $summary['percentage'] = ceil(($collect / $total_request) * 100);
        }

        return $summary;
    }

    public static function guzzleInitiator()
    {
        $guzzle = new guzzleClient([
            'connect_timeout' => 86400.0,
            'timeout'   => 86400.0,
            'verify' => false,
            'cookies' => true,
        ]);
        return $guzzle;
    }
    public static function getSolrResult($core, $search)
    {
        $guzzle = self::guzzleInitiator();
        $scheme = env('SOLR_SCHEME', 'http');
        $host = env('SOLR_HOST', 'localhost');
        $port = env('SOLR_PORT', '8983');
        $solr = $scheme . '://' . $host . ':' . $port . '/solr/' . $core . '/select?q=';
        $response = $guzzle->get($solr . $search);
        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);
        return $content;
    }

    public static function getSolrNumFound($core, $search)
    {
        $data = self::getSolrResult($core, $search);
        return $data["response"]["numFound"];
    }
    public static function downloadData($core, $table, $data = [])
    {
        $result = [];
        $client = self::solrClient($core);

        $solr_query = $client->createSelect();
        $solr_query->createFilterQuery($table)->setQuery('table_name:' . $table);

        if (array_key_exists('id', $data) && $core == 'isbn') {
            $solr_query->createFilterQuery('table_id')->setQuery('table_id:' . $data['id']);
        } else {
            foreach ($data as $key => $d) {
                $solr_query->createFilterQuery($key)->setQuery($key . ':' . $d);
            }
        }
        $getNumFoundQuery = $solr_query;
        $numFound = $client->execute($getNumFoundQuery)->getNumFound();
        $result = [];
        for ($i = 0; $i < ceil($numFound / 10000); $i++) {
            $solr_query_ = $solr_query->setStart(10000 * $i)->setRows(2000);
            $result_solr = $client->execute($solr_query_);
            foreach ($result_solr as $rs) {
                $result[] = $rs;
            }
        }
        return $result;
    }

    public static function datatable($core, $table, $data = [], $pagination = [], $specific = [])
    {
        $response = [];
        $client   = self::solrClient($core);

        $solr_query = $client->createSelect();
        $solr_query->createFilterQuery('table_name')->setQuery('table_name:' . $table);

        if ($specific) {
            foreach ($specific as $key => $s) {
                $solr_query->createFilterQuery($key)->setQuery($key . ':' . $s);
            }
        }

        if ($data) {
            foreach ($data as $key => $d) {
                $solr_query->createFilterQuery($key)->setQuery($key . ':' . $d);
            }
        }


        if ($pagination['sort'] == 'desc') {
            $solr_query->addSort($pagination['column'], $solr_query::SORT_DESC);
        } else {
            $solr_query->addSort($pagination['column'], $solr_query::SORT_ASC);
        }

        $solr_query->setStart($pagination['offset'])->setRows($pagination['limit']);
        $result_solr = $client->execute($solr_query);

        foreach ($result_solr as $rs) {
            $response[] = $rs;
        }

        return [
            'start'          => $pagination['offset'],
            'row'            => $pagination['limit'],
            'total_all_data' => self::data($core, $table, $specific, [], true),
            'total_filter'   => $result_solr->getNumFound(),
            'result'         => $response
        ];
    }

    public static function testPing()
    {
        try {
            $client   = self::solrClient('isbn');
            $ping = $client->createPing();
            $client->ping($ping);
            return response()->json('OK');
        } catch (\Exception $e) {
            dd($e);
            return response()->json('ERROR', 500);
        }
    }
}
