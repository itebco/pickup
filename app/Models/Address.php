<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'owner_name', 'tel', 'post_code',
        'state', 'city', 'ward', 'type', 'room_no', 'created_by',
    ];

    public const TYPE_MANSION = 'mansion';
    public const TYPE_APARTMENT = 'apartment';

    public function packages()
    {
        return $this->hasMany(Package::class, 'address_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getFullAddressAttribute()
    {
        return ' (' . $this->post_code . ')' . ' ' . $this->state . ' ' . $this->city . ' ' . $this->ward . ' ' . $this->room_no;
    }

    public function getTranslatedTypeAttribute()
    {
        return Lang::get('address.' . $this->type);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
