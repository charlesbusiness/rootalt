<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Modules\Authentication\Dto\UserDto;
use Modules\BusinessManager\Models\Business;
use Modules\BusinessManager\Models\Employee;
use Modules\Core\Models\BusinessRole;
use Modules\Core\Models\BusinessRoleUser;
use Modules\Core\Models\Country;
use Modules\Core\Models\Role;
use Modules\Core\Services\CoreService;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_enabled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Relationship with Role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship with Role.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }



    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role?->name === $role;
    }

    /**
     * The attributes that are appendable.
     *
     * @var array<int, string>
     */

    protected $appends = ['user_role',];


    /**
     * Accessor for appended 'role' attribute.
     */
    public function getUserRoleAttribute()
    {
        return $this->role?->name;
    }



    /**
     * Accessor for appended 'role' attribute.
     */
    public function getCountryDetailAttribute()
    {
        return $this->country;
    }

    public function createUser(UserDto $dto)
    {
        $user = $this->create([
            'email' => $dto->email,
            'phone' => $dto->phone,
            'dob' => $dto->dob,
            'firstName' => $dto->firstName,
            'lastName' => $dto->lastName,
            'username' => $dto->username,
            'password' => Hash::make($dto->password),
        ]);
        $referralCode = null;
        if ($dto->referralCode) {

            $referralCode = $dto->referralCode;
        }
        return ['user' => $user, 'code' => $referralCode];
    }

    public function businessRole()
    {
        return $this->belongsToMany(BusinessRole::class, 'business_role_users');
    }

    // Accessor to get all permissions from all roles as a collection of permission names.
    public function getAllPermissionsAttribute()
    {
        return $this->businessRole()
            ->with('permissions.resource')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique(function ($permission) {
                return $permission->id;
            });
    }




    public function businessRoles()
    {
        return $this->belongsToMany(BusinessRole::class, 'business_role_users');
    }


    public function hasSuperAccessToResource()
    {
        if ($this->businessRoles->contains(function ($role) {
            return $role->name === CoreService::MAIN_BUSINESS_ADMIN && is_null($role->business_id);
        })) {
            return true;
        }
    }


    public function hasAccessToResource($resourceId, $businessId)
    {
        // Check if the user has the global 'admin-user' role
        $resourceIds = $this->businessRoles
            ->flatMap(function ($role) use ($businessId) {
                return $role->businessRolePermissions
                    ->when($businessId, function ($query, $businessId) {
                        return $query->where('business_id', $businessId);
                    })
                    ->pluck('resource_id');
            })->unique();

        return $resourceIds->contains($resourceId);
    }

    public function hasAccessToResource2($resourceId, $businessId = null)
    {
        // Check if the user has the global 'admin-user' role
        if ($this->businessRoles->contains(function ($role) {
            return $role->name === CoreService::MAIN_BUSINESS_ADMIN && is_null($role->business_id);
        })) {
            return true;
        }

        $resourceIds = $this->businessRoles
            ->flatMap(function ($role) use ($businessId) {
                // Optionally filter by business if needed
                return $role->businessRolePermissions
                    ->when($businessId, function ($query, $businessId) {
                        return $query->where('business_id', $businessId);
                    })
                    ->pluck('resource_id');
            })->unique();

        return $resourceIds->contains($resourceId);
    }


    public function businessRoleUsers()
    {
        return $this->hasMany(BusinessRoleUser::class);
    }

    public function rolesInBusiness($businessId)
    {
        info("Business role user data", [$this->businessRoleUsers()]);
        return $this->businessRoleUsers()
            ->where('business_id', $businessId)
            ->with('businessRole:id,name,business_id')
            ->get()
            ->pluck('businessRole');
    }
}
