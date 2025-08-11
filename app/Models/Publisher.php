<?php

namespace App\Models;

use App\Models\Collection;
use Illuminate\Support\Str;
use App\Models\PublisherGroup;
use App\Models\PublisherAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{

    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table      = 'publishers';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'organization_id',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'photo',
        'publisher_code',
        'contact',
        'name',
        'name_change',
        'fax',
        'phone',
        'website',
        'address',
        'postal_code',
        'type',
        'code_system',
        'system_type',
        'birth_certificate',
        'statement_letter',
        'birth_certificate_location',
        'statement_letter_location',
        'status'
    ];

    public function authClient()
    {
        return $this->morphOne('App\Models\AuthClient', 'authable');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District');
    }

    public function village()
    {
        return $this->belongsTo('App\Models\Village');
    }

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    public function warning()
    {
        return $this->HasMany('App\Models\PublisherWarning');
    }

    public function type()
    {
        if ($this->type == 1) {
            $type = 'Swasta';
        } else if ($this->type == 2) {
            $type = 'Perorangan';
        } else if ($this->type == 3) {
            $type = 'Pemerintah';
        } else {
            $type = 'Invalid';
        }

        return $type;
    }

    public function status()
    {
        if ($this->status == 1) {
            $status = 'Review';
        } else if ($this->status == 2) {
            $status = 'Masalah';
        } else if ($this->status == 3) {
            $status = 'Tervalidasi';
        } else {
            $status = 'Invalid';
        }

        return $status;
    }

    public function collection()
    {
        return $this->hasMany('App\Models\Collection');
    }

    public function user()
    {
        return $this->morphOne('App\Models\User', 'userable');
    }

    public function photo()
    {
        if ($this->photo && Storage::exists($this->photo)) {
            return asset(Storage::url($this->photo));
        } else {
            return asset('main/user.png');
        }
    }

    public function publisherAccess()
    {
        return $this->hasMany('App\Models\PublisherAccess');
    }

    public function getTotalCollection($type)
    {
        return Collection::where('type', $type)
            ->where('status', 2)
            ->where('publisher_id', $this->id)
            ->count();
    }

    public function checkSameGroups($other_publisher_id)
    {
        if ($publisherGroups = $this->getGroups()) {
            if ($publisherGroups->groups->where('publisher_id', $other_publisher_id)->first()) {
                return true;
            }
        }

        return false;
    }

    public function checkAccess($publisher_real_id, $publisher_other_id)
    {
        $access_real  = PublisherAccess::where('publisher_id', $publisher_real_id)->pluck('publisher_group_id')->toArray();
        $access_other = PublisherAccess::where('publisher_id', $publisher_other_id)->pluck('publisher_group_id')->toArray();

        foreach ($access_other as $ao) {
            if (in_array($ao, $access_real)) {
                return true;
            }
        }

        return false;
    }

    public function getPublisherByGroup()
    {
        if ($this->publisherAccess->count() > 0) {
            $arr = [];
            foreach ($this->publisherAccess as $pa) {
                $get_access_group = PublisherAccess::where('publisher_group_id', $pa->publisher_group_id)->get();
                foreach ($get_access_group as $gap) {
                    $arr[] = $gap->publisher_id;
                }
            }

            return $arr;
        } else {
            return [$this->id];
        }
    }

    public function getGroups()
    {
        $publisher_access = PublisherAccess::select('publisher_group_id')
            ->where('publisher_id', $this->id)
            ->first();

        if ($publisher_access) {
            return PublisherGroup::where('id', $publisher_access->publisher_group_id)->first();
        }

        return null;
    }

    public static function checkGroupPublisher($publisher, $publisherName)
    {
        $groups = $publisher->getGroups();

        if ($groups == null) {
            if ($publisher->name == $publisherName) {
                return $publisher;
            } else {
                return null;
            }
        }

        $publisherGroups = $groups->groups->pluck('publisher_id');

        $result = Publisher::where(function ($query) use ($publisherName, $publisherGroups) {
            $query->whereIn('id', $publisherGroups)
                ->where(DB::raw('lower(name)'), '=', Str::lower($publisherName));
        })
            ->first();

        return $result;
    }
}
