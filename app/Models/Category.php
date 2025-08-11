<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'categories';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'name',
        'slug',
        'type',
    ];

    public function collectionCategory()
    {
        return $this->hasMany('App\Models\CollectionCategory');
    }

    public function countOfCollection()
    {
        return CollectionCategory::select('id')->where('category_id', $this->id)->count();
    }

    public function type()
    {
        // if($this->type == 1) {
        //     $type = 'Buku';
        // } else if($this->type == 2) {
        //     $type = 'Partitur';
        // } else if($this->type == 3) {
        //     $type = 'Peta';
        // } else if($this->type == 4) {
        //     $type = 'Serial';
        // } else if($this->type == 5) {
        //     $type = 'Audio';
        // } else if($this->type == 6) {
        //     $type = 'Film';
        // } else {
        //     $type = 'Invalid';
        // }

        // return $type;

        $type = DepositHead::select('shape')->where('id', $this->type)->first()->shape;
        return isset($type) ? $type : 'invalid';
    }
}
