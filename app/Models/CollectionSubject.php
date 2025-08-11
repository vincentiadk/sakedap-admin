<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionSubject extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_subjects';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_id',
        'subject_id'
    ];

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function subject()
    {
        return $this->belongsTo('App\Models\Subject');
    }
}
