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

    public $roles = ['ADMIN' => 'admin', 'USER' => 'users'];

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
    public function getRoles($roleName)
    {
        return Role::query()
            ->where('name', $roleName)
            ->first();
    }

    public function createUser(UserDto $dto)
    {
        $user = $this->create([
            'email' => $dto->email,
            'phone' => $dto->phone,
            'role_id' => $this->getRoles($this->roles['USER'])->id,
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
}
