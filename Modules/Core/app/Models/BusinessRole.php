<?php

namespace Modules\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BusinessManager\Models\Business;

// use Modules\Core\Database\Factories\BusinessRoleFactory;

class BusinessRole extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_role_users');
    }

    public function businessRolePermissions()
    {
        return $this->hasMany(BusinessRolePermission::class);
    }

    // 

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function businessRoleUsers()
    {
        return $this->hasMany(BusinessRoleUser::class);
    }

    public function businessRole()
    {
        return $this->belongsTo(BusinessRole::class);
    }

    public function permissions()
    {
        return $this->hasMany(BusinessRolePermission::class);
    }

    public function scopeRoleQuery($query, $businessId)
    {
        return $query->where('business_id', $businessId)
            ->orWhereNull('business_id');
    }
}
