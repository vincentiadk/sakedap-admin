<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'marks';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'deposit_head_id',
        'province_id',
        'regency_id',
        'year',
        'last_digit',
        'missing_digit'
    ];

    public function deposit_head()
    {
        return $this->belongsTo('App\Models\DepositHead');
    }
}
