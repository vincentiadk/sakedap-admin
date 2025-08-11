<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CopyRejectedProblem extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'copy_rejected_problem';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'copy_rejected_id',
        'problem_id',
        'note',
        'updated_by',
        'created_by'
    ];

    public function copyRejected()
    {
        return $this->belongsTo('App\Models\CopyRejected', 'copy_rejected_id');
    }

    public function problem()
    {
        return $this->belongsTo('App\Models\Problem', 'problem_id');
    }
}
