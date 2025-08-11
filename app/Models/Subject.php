<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'subjects';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'name',
        'slug',
    ];

    public function countOfCollection()
    {
        return CollectionSubject::select('id')->where('subject_id', $this->id)->count();
    }

    public function collectionSubject()
    {
        return $this->hasMany('App\Models\CollectionSubject');
    }
}
