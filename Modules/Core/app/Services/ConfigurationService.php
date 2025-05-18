<?php

namespace Modules\Core\Services;

use Modules\Core\Models\Country;
use Modules\Core\Models\County;
use Modules\Core\Models\Industry;
use Modules\Core\Models\PaymentOption;
use Modules\Core\Models\Role;


class ConfigurationService extends CoreService
{
   
    protected $role;
    protected $industry;
    protected $country;
    protected $servicePriceConfig;
    protected $county;

    public function __construct(
       
        Role $role,
        Industry $industry,
        Country $country,
        PaymentOption $option,
        County $county
    ) {
        $this->country = $country;
        $this->role = $role;
        $this->industry = $industry;
        $this->county = $county;
    }

  

    /**
     * Create roles 
     */
    public  function createRoles()
    {
        $roles = $this->role->getRoles();

        $this->createOrUpdateMultiple($roles, $this->role, ['key']);
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
}
