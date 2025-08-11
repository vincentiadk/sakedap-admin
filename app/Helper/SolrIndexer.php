<?php

namespace App\Helper;

use Illuminate\Support\Collection as Coll;
use App\Collection;
use App\CollectionFile2;
use Log;

class SolrIndexer {

	protected $client;

	public function __construct()
	{
		$this->client = new \Solarium\Client(config('solarium'));
	}

	public function addDoc($doc)
	{
		$update = $this->client->createUpdate();
		$update->addDocument($doc);
		$update->addCommit();
		$this->client->update($update);
	}

	public function deleteBySolrId($solr_id)
	{
		$update = $this->client->createUpdate();
		$update->addDeleteById($solr_id);
		$update->addCommit();
		$result = $client->update($update);
	}

	public function deleteById($table_name, $table_id)
	{
		$update = $this->client->createUpdate();
         // add the delete query and a commit command to the update query
		if($table_id != "" && $table_name !="") {
			$update->addDeleteQuery("table_name:".$table_name." AND table_id:".$table_id);

			$update->addCommit();
        // this executes the query and returns the result
			$result = $this->client->update($update);
		}
		return true;
	}

	public function searchById($table_name, $table_id)
	{
		$query = $this->client->createSelect();

		$query->createFilterQuery('table_name')->setQuery("table_name:".$table_name);   
		$query->createFilterQuery('table_id')->setQuery("table_id:".$table_id);

		$resultset = $this->client->select($query);
		$return = $this->getResult($table_name, $resultset);

		return $return;
	}

	public function addCollectionDoc(Collection $data, $array = false)
	{    
		$doc1 = [];		
		$dataArray = $data->toArray();
		unset($dataArray["collection_files"]);
		unset($dataArray["coll_problem"]);
		unset($dataArray["publisher_tb"]);
		unset($dataArray["meta_type"]);
		unset($dataArray["created_by"]);
		unset($dataArray["updated_by"]);
		unset($dataArray["edit_by"]);
		unset($dataArray["validated_by"]);

		$doc1["table_name"] = "collections";

		foreach($dataArray as $key => $value) {
			switch($key) {
				case "id_coll" : 
				$doc1["table_id"] = $value;
				break;

				case "date" :
				if($value != "") {
					$unDate = @unserialize($value);
					$unDate["tanggal_terbit"] = $unDate["tanggal_terbit"] != "" ? date('Y-m-d\Th:m:s\Z', strtotime($unDate["tanggal_terbit"])) : "";
					$unDate["tanggal_serah"] = $unDate["tanggal_serah"] != "" ? date('Y-m-d\Th:m:s\Z', strtotime($unDate["tanggal_serah"])) : "";
					$unDate["tanggal_terima"] = $unDate["tanggal_terima"] != "" ? date('Y-m-d\Th:m:s\Z', strtotime($unDate["tanggal_terima"])) : "";
					$doc1["date"] = $unDate;
				}
				break;

				case "id_type" :
				$doc["meta_type"] = $data->meta_type->name;
				$doc[$key] = $value;
				break;

				case "created_at" :
				$doc1["created_at"] =  date('Y-m-d\Th:m:s\Z', strtotime($value));
				break;

				case "edit_at" :
				$doc1["edit_at"] = ($data->edit_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "";
				break;

				case "validated_at" :
				$doc1["validated_at"] = ($data->validated_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "";
				break;

				case "updated_at" :
				$doc1["updated_at"] = ($data->updated_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "";
				break;

				case "international_identifier" :
				$doc1["international_identifier"] = str_replace(['-',' '],'', $value);
				break;

				default : 
				$unserializeValue = @unserialize($value);
				if ($unserializeValue !== false) {
					$doc1[$key] = $unserializeValue;
				} else {
					$doc1[$key] = $value;
				}
				break;
			}
		}	

		$doc1["created_by_name"] = $data->createdBy ? $data->createdBy->fullname : "";
		$doc1["updated_by_name"] = $data->updatedBy ? $data->updatedBy->fullname : "";
		$doc1["edit_by_name"] = $data->editBy ? $data->editBy->fullname : "";
		$doc1["validated_by_name"] = $data->validatedBy ? $data->validatedBy->fullname : "";

		if($data->publisher_tb) {
			$doc1["publisherName"] = $data->publisher_tb->name;
			$doc1["publisherProvince"] = $data->publisher_tb->province ? $data->publisher_tb->province->name : "";
			$doc1["publisherCity"] = $data->publisher_tb->city ? $data->publisher_tb->city->name : "";
			$doc1["publisherDistrict"] = $data->publisher_tb->district ? $data->publisher_tb->district->name : "";
			$doc1["publisherVillage"] = $data->publisher_tb->village ? $data->publisher_tb->village->name : "";
		} else {
			$doc1["publisherName"] = "";
			$doc1["publisherProvince"] = "";
			$doc1["publisherCity"] = "";
			$doc1["publisherDistrict"] = "";
			$doc1["publisherVillage"] = "";
		}

		if($array) {
			return $doc1;
		} else {
			$update = $this->client->createUpdate();
			return  $update->createDocument($doc1);
		}
	}

	public function addCollectionFileDoc(CollectionFile2 $data, $array = false)
	{    
		$doc1 = [];
		if($docId != "") {
			$doc1["id"] = $docId;
		}		
		$dataArray = $data->toArray();
		unset($dataArray["collection"]);
		unset($dataArray["coll_file_logs"]);
		unset($dataArray["publisher_tb"]);
		unset($dataArray["created_by"]);
		unset($dataArray["updated_by"]);

		$doc1["table_name"] = "collection_files";

		foreach($dataArray as $key => $value) {
			switch($key) {
				case "id" : 
				$doc1["table_id"] = $value;
				break;

				case "created_at" :
				$doc1["created_at"] = ($value != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "";
				break;

				case "updated_at" :
				$doc1["updated_at"] = ($value != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "";
				break;

				default : 
				$doc1[$key] = $value;
				break;
			}
		}
		$doc1["created_by_name"] = $data->createdBy ? $data->createdBy->fullname : "";
		$doc1["updated_by_name"] = $data->updatedBy ? $data->updatedBy->fullname : "";

		if($array) {
			return $doc1;
		} else {
			$update = $this->client->createUpdate();
			return  $update->createDocument($doc1);
		}
	}

	public function updateCollectionDoc(Collection $data, $docId)
	{
		$update = $this->client->createUpdate();
		$doc1 = $update->createDocument();
		$doc1->setKey('id', $docId);
		$dataArray = $data->toArray();
		unset($dataArray["collection_files"]);
		unset($dataArray["coll_problem"]);
		unset($dataArray["publisher_tb"]);
		unset($dataArray["meta_type"]);
		unset($dataArray["created_by"]);
		unset($dataArray["updated_by"]);
		unset($dataArray["edit_by"]);
		unset($dataArray["validated_by"]);

		foreach($dataArray as $key => $value) {
			switch($key) {
				case "id_coll" : 
				break;

				case "international_identifier" :
				$doc1->setField($key, str_replace(['-',' '],'', $value), null, $doc1::MODIFIER_SET);
				break;

				case "date" :
				if($value != "") {
					$unDate = @unserialize($value);
					$unDate["tanggal_terbit"] = $unDate["tanggal_terbit"] != "" ? date('Y-m-d\Th:m:s\Z', strtotime($unDate["tanggal_terbit"])) : "";
					$unDate["tanggal_serah"] = $unDate["tanggal_serah"] != "" ? date('Y-m-d\Th:m:s\Z', strtotime($unDate["tanggal_serah"])) : "";
					$unDate["tanggal_terima"] = $unDate["tanggal_terima"] != "" ? date('Y-m-d\Th:m:s\Z', strtotime($unDate["tanggal_terima"])) : "";
					$doc1->setField($key, $unDate, null, $doc1::MODIFIER_SET);
				}
				break;

				case "created_at" :
				$doc1->setField($key, date('Y-m-d\Th:m:s\Z', strtotime($value)), null, $doc1::MODIFIER_SET);
				break;

				case "edit_at" :
				$doc1->setField($key, ($data->edit_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "", null, $doc1::MODIFIER_SET);
				break;

				case "validated_at" :
				$doc1->setField($key, ($data->validated_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "", null, $doc1::MODIFIER_SET);
				break;

				case "updated_at" :
				$doc1->setField($key, ($data->updated_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "", null, $doc1::MODIFIER_SET);
				break;

				case "international_identifier" :
				$doc1->setField($key, str_replace(['-',' '],'', $value), null, $doc1::MODIFIER_SET) ;
				break;

				default : 
				$unserializeValue = @unserialize($value);
				if ($unserializeValue !== false) {
					$doc1->setField($key, $unserializeValue, null, $doc1::MODIFIER_SET);
				} else {
					$doc1->setField($key, $value, null, $doc1::MODIFIER_SET);
				}
				break;
			}		
		}
		$doc1->setField("created_by_name", $data->createdBy ? $data->createdBy->fullname : "", null, $doc1::MODIFIER_SET );
		$doc1->setField("updated_by_name", $data->updatedBy ? $data->updatedBy->fullname : "", null, $doc1::MODIFIER_SET );
		$doc1->setField("edit_by_name", $data->editBy ? $data->editBy->fullname : "", null, $doc1::MODIFIER_SET );
		$doc1->setField("validated_by_name", $data->validatedBy ? $data->validatedBy->fullname : "", null, $doc1::MODIFIER_SET );
		if($data->publisher_tb) {
			$doc1->setField("publisherName", $data->publisher_tb->name, null, $doc1::MODIFIER_SET );
			$doc1->setField("publisherProvince", $data->publisher_tb->province ? $data->publisher_tb->province->name : "", null, $doc1::MODIFIER_SET);
			$doc1->setField("publisherCity", $data->publisher_tb->city ? $data->publisher_tb->city->name : "", null, $doc1::MODIFIER_SET);
			$doc1->setField("publisherDistrict", $data->publisher_tb->district ? $data->publisher_tb->district->name : "", null, $doc1::MODIFIER_SET);
			$doc1->setField("publisherVillage", $data->publisher_tb->village ? $data->publisher_tb->village->name : "", null, $doc1::MODIFIER_SET);
		} 
		$update->addDocument($doc1);
		$update->addCommit();
		$this->client->update($update);
	}

	public function updateCollectionFileDoc(Collection $data, $docId)
	{
		$update = $this->client->createUpdate();
		$doc1 = $update->createDocument();
		$doc1->setKey('id', $docId);
		$dataArray = $data->toArray();
		unset($dataArray["collection"]);
		unset($dataArray["coll_file_logs"]);
		unset($dataArray["publisher_tb"]);
		unset($dataArray["created_by"]);
		unset($dataArray["updated_by"]);

		foreach($dataArray as $key => $value) {
			switch($key) {
				case "id" : 
				break;

				case "created_at" :
				$doc1->setField($key, date('Y-m-d\Th:m:s\Z', strtotime($value)), null, $doc1::MODIFIER_SET);
				break;

				case "updated_at" :
				$doc1->setField($key, ($data->updated_at != "") ? date('Y-m-d\Th:m:s\Z', strtotime($value)) : "", null, $doc1::MODIFIER_SET);
				break;

				default : 
				$doc1->setField($key, $value, null, $doc1::MODIFIER_SET);
				break;
			}		
		}
		$doc1->setField("created_by_name", $data->createdBy ? $data->createdBy->fullname : "", null, $doc1::MODIFIER_SET );
		$doc1->setField("updated_by_name", $data->updatedBy ? $data->updatedBy->fullname : "", null, $doc1::MODIFIER_SET );
		$update->addDocument($doc1);
		$update->addCommit();
		$this->client->update($update);
	}

	public function getResult($table_name, $resultset)
	{
		$model = new Coll;
		switch($table_name) {
			case "collections" :
			foreach($resultset as $document ) {
				$model->push([
					"solr_id" => $document->id,
					"id_coll" => $document->table_id, 
					"title" => $document->title,
					"date" => $document->date,
					"international_identifier" => $document->international_identifier,
					"international_identifier_type" => $document->international_identifier_type,
					"publisher" => $document->publisher,
					"publisherName" => $document->publisherName,
				]);
			}
			break;
			default:break;
		}
		return $model;
	}
}