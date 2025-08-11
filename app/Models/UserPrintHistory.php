<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPrintHistory extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $connection = 'mysql';
    protected $table      = 'user_print_histories';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'created_by',
        'updated_by',
        'collection_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }
}
