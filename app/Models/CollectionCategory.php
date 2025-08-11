<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionCategory extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'collection_categories';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'collection_id',
        'category_id'
    ];

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
}
