<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    protected $connection = 'mysql';
    protected $table      = 'activity_log';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'created_at',
        'updated_at'
    ];

    public function getUpdatedAtAttribute($value)
    {
        return date('d-m-Y H:i:s', strtotime($value));
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'causer_id', 'id');
    }

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection', 'subject_id', 'id');
    }
}
