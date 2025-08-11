<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BookImport implements WithMultipleSheets
{   
    private $folderName;
    private $userId;
    private $sessionName;
    private $sessionNameFailed;
    private $typeOfCollection;

    function __construct($folderName, $userId, $sessionName, $sessionNameFailed, $typeOfCollection) {
        $this->folderName = $folderName;
        $this->userId = $userId;
        $this->sessionName = $sessionName;
        $this->sessionNameFailed = $sessionNameFailed;
        $this->typeOfCollection = $typeOfCollection;
    }

    public function sheets(): array
    {
        return [
            0 => new FirstSheetBookImport($this->folderName, $this->userId, $this->sessionName, $this->sessionNameFailed, $this->typeOfCollection),
        ];
    }

}