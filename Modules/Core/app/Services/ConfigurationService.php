<?php

namespace Modules\Core\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\CommissionLevel;
use Modules\Core\Models\Country;
use Modules\Core\Models\County;
use Modules\Core\Models\Industry;
use Modules\Core\Models\Role;


class ConfigurationService extends CoreService
{

    protected $role;
    protected $industry;
    protected $country;
    protected $servicePriceConfig;
    protected $county;
    protected $user;
    protected $adminUserName;
    protected $adminLastName;
    protected $adminFirstName;
    protected $adminPassword;
    protected $adminEmail;
    protected $adminPhone;
    protected $commissionLevel;

    public function __construct(

        Role $role,
        Industry $industry,
        Country $country,
        County $county,
        User $user,
        CommissionLevel $commissionLevel
    ) {
        $this->country = $country;
        $this->role = $role;
        $this->industry = $industry;
        $this->county = $county;
        $this->commissionLevel = $commissionLevel;
        $this->user = $user;
        $this->adminUserName = config('core.default_admin.username');
        $this->adminLastName = config('core.default_admin.lastname');
        $this->adminFirstName = config('core.default_admin.firstname');
        $this->adminPassword = config('core.default_admin.password');
        $this->adminPhone = config('core.default_admin.phone');
        $this->adminEmail = config('core.default_admin.email');
    }

    /**
     * Create an Admin User
     */

    public function createDefaultAdminUser()
    {

        return  $this->user->updateOrCreate(
            ['email' => $this->adminEmail],
            [
                'email' => $this->adminEmail,
                'phone' => $this->adminPhone,
                'role_id' => $this->user->getRoles($this->user->roles['ADMIN'])->id,
                'dob' => now()->subYears(30),
                'email_verified_at' => now(),
                'firstName' => $this->adminFirstName,
                'lastName' => $this->adminLastName,
                'username' => $this->adminUserName,
                'password' => Hash::make($this->adminPassword),
            ]
        );
    }

    /**
     * Create roles 
     */
    public  function createRoles()
    {
        $roles = ['admin', 'users'];
        foreach ($roles as $role) {
            $this->role->updateOrCreate(['name' => $role]);
        }
    }

    /**
     * Create Industries 
     */
    public  function createIndustries()
    {
        $industry = $this->industry->getIndustries();

        $this->createOrUpdateMultiple($industry, $this->industry, ['name']);
    }


    /**
     * Create Industries 
     */
    public  function createCountries()
    {
        $country = $this->country->getCountries();

        $this->createOrUpdateMultiple($country, $this->country, ['name']);
    }


    /**
     * Create Counties for Liberia 
     */
    public  function createCounties()
    {
        $counties = $this->county->getCounties();

        foreach ($counties as $county) {

            $this->county->create(['county_name' => $county]);
        }
    }



    /**
     * Get country lists and all associated information
     */

    public function getCountries()
    {
        $countries = $this->country
            ->select(['name', 'phone_code as dail_code', 'currency_code', 'id', 'currency_name', 'code as country_code'])
            ->orderBy('id', 'DESC')
            ->get();
        return successfulResponse($countries);
    }


    /**
     * Get counties
     */

    public function getCounties()
    {
        $countries = $this->county
            ->select(['county_name', 'id'])
            ->orderBy('id', 'DESC')
            ->get();
        return successfulResponse($countries);
    }


    /**
     * Get country lists and all associated information
     */

    public function industries()
    {
        $industries = $this->industry
            ->select(['name', 'description', 'id'])
            ->orderBy('id', 'DESC')
            ->get();
        return successfulResponse($industries);
    }

    /**
     * Get country lists and all associated information
     */

    public function createCommissionLevel()
    {
        return $this->commissionLevel->createLevel();
    }
}
