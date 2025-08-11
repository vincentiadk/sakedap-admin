<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkDetail extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'bulk_details';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'bulk_id',
        'title',
        'description',
        'status'
    ];

    public function status()
    {
        if ($this->status == 1) {
            $status = 'Berhasil';
        } else if ($this->status == 2) {
            $status = 'Gagal';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }
}
