<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'faq';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'question',
        'answer',
        'category',
        'sequence',
        'publish'
    ];
}
