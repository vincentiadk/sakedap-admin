<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PartiturImport implements WithMultipleSheets
{   
    private $folderName;
    private $userId;
    private $sessionName;

    function __construct($folderName, $userId, $sessionName) {
        $this->folderName = $folderName;
        $this->userId = $userId;
        $this->sessionName = $sessionName;
    }

    public function sheets(): array
    {
        return [
            0 => new FirstSheetPartiturImport($this->folderName, $this->userId, $this->sessionName)
        ];
    }

}
