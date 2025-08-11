<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulk extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'bulks';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'user_id',
        'name',
        'file',
        'process_start_at',
        'process_finish_at',
        'status',
        'deposit_head_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function status()
    {
        if ($this->status == 1) {
            $status = 'Selesai';
        } else if ($this->status == 2) {
            $status = 'Diproses';
        } else if ($this->status == 3) {
            $status = 'Gagal diproses';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }

    public function bulkDetail()
    {
        return $this->hasMany('App\Models\BulkDetail');
    }

    public function depositHead()
    {
        return $this->belongsTo('App\Models\DepositHead', 'deposit_head_id');
    }
}
