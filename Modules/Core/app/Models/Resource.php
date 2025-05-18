<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\ResourceFactory;

class Resource extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name','is_action_base', 'endpoint','module_name', 'description', 'is_public'];

    public function businessRolePermissions()
    {
        return $this->hasMany(BusinessRolePermission::class);
    }

    public function businessRoles()
    {
        return $this->belongsToMany(BusinessRole::class, 'business_role_permissions');
    }

    public function scopeIsPublicResource($query)
    {
        return $query->where('is_public',true)
        ->select('description','name','endpoint','id', 'module_name', 'is_action_base');
    }
}
