<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BusinessManager\Models\Business;

// use Modules\Core\Database\Factories\BusinessPermissionFactory;

class BusinessPermission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['business_id', 'name'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function roles()
    {
        return $this->belongsToMany(BusinessRole::class, 'business_role_permissions');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
