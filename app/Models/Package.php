<?php

namespace App\Models;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'package_code', 'address_id', 'pickup_date', 'pickup_time',
        'quantity', 'method', 'status', 'remark', 'created_by', 'user_id',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';

    protected static function boot()
    {
        parent::boot();

        // Automatically generate package code before creating
        static::creating(function ($package) {
            if (empty($package->package_code)) {
                $package->package_code = CommonHelper::generatePackageCode();
            }
        });
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getTranslatedStatusAttribute()
    {
        return Lang::get('package.status.' . $this->status);
    }

    public function getTranslatedMethodAttribute()
    {
        return Lang::get('package.method.' . $this->method);
    }
}
