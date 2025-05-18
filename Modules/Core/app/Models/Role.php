<?php

namespace Modules\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'key'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Define the relationship with permissions.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Define the relationship with users.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    protected $roles = [
        ['key' => 'business-admin', 'name' => 'Business Admin'],
        ['key' => 'business-account-manager', 'name' => 'Business Account Manager'],
        ['key' => 'business-editor', 'name' => 'Business Editor'],
        ['key' => 'business-viewer', 'name' => 'Business Viewer'],
        ['key' => 'individual-admin', 'name' => 'Individual Admin'],
        ['key' => 'tech-admin', 'name' => 'Technical Administrator'],
        ['key' => 'tech-support', 'name' => 'Technical Support'],
    ];

    /**
     * Return the defined roles
     *@return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
