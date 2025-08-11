<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SerialImport implements WithMultipleSheets
{   
    private $folderName;
    private $userId;
    private $collectionId;
    private $sessionName;

    function __construct($folderName, $userId, $collectionId, $sessionName) {
        $this->folderName = $folderName;
        $this->userId = $userId;
        $this->collectionId = $collectionId;
        $this->sessionName = $sessionName;
    }

    public function sheets(): array
    {
        return [
            0 => new FirstSheetSerialImport($this->folderName, $this->userId, $this->collectionId, $this->sessionName)
        ];
    }

}
