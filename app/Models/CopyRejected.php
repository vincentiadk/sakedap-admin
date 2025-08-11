<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CopyRejected extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = 'mysql';
    protected $table      = 'copy_rejected';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_copy_id',
        'handling',
        'copy_rejected',
        'updated_by',
        'created_by'
    ];

    public function copy()
    {
        return $this->belongsTo('App\Models\CollectionCopy', 'collection_copy_id');
    }

    public function copy_rejected_problem()
    {
        return $this->hasMany('App\Models\CopyRejectedProblem', 'copy_rejected_id');
    }
}
