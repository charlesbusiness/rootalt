<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\BusinessRolePermissionFactory;

class BusinessRolePermission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['business_id', 'business_role_id', 'resource_id'];


    public function businessRole()
    {
        return $this->belongsTo(BusinessRole::class);
    }

    // You might have a Resource model; if so, set up the relationship:
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
