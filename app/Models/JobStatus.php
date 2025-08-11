<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{

    const STATUS_QUEUED    = 'queued';
    const STATUS_EXECUTING = 'executing';
    const STATUS_FINISHED  = 'finished';
    const STATUS_FAILED    = 'failed';
    const STATUS_RETRYING  = 'retrying';

    protected $connection = 'mysql';
    public $dates         = ['started_at', 'finished_at', 'created_at', 'updated_at'];
    protected $guarded    = [];

    protected $casts = [
        'input'  => 'array',
        'output' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = session('id');
        });
    }

    public function type()
    {
        if (!isset($this->input['type_id'])) {
            return 'invalid';
        }

        if ($this->input['type_id'] == 1) {
            $type = 'Buku';
        } else if ($this->input['type_id'] == 2) {
            $type = 'Partitur';
        } else if ($this->input['type_id'] == 3) {
            $type = 'Peta';
        } else if ($this->input['type_id'] == 4) {
            $type = 'Serial';
        } else if ($this->input['type_id'] == 5) {
            $type = 'Audio';
        } else if ($this->input['type_id'] == 6) {
            $type = 'Film';
        } else {
            $type = 'Invalid';
        }

        return $type;
    }

    public function status()
    {
        return $this->status;
    }

    /* Accessor */
    public function getProgressPercentageAttribute()
    {
        return $this->progress_max !== 0 ? round(100 * $this->progress_now / $this->progress_max) : 0;
    }

    public function getIsEndedAttribute()
    {
        return \in_array($this->status, [self::STATUS_FAILED, self::STATUS_FINISHED], true);
    }

    public function getIsFinishedAttribute()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function getIsFailedAttribute()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getIsExecutingAttribute()
    {
        return $this->status === self::STATUS_EXECUTING;
    }

    public function getIsQueuedAttribute()
    {
        return $this->status === self::STATUS_QUEUED;
    }

    public function getIsRetryingAttribute()
    {
        return $this->status === self::STATUS_RETRYING;
    }

    public static function getAllowedStatuses()
    {
        return [
            self::STATUS_QUEUED,
            self::STATUS_EXECUTING,
            self::STATUS_FINISHED,
            self::STATUS_FAILED,
            self::STATUS_RETRYING,
        ];
    }
}
