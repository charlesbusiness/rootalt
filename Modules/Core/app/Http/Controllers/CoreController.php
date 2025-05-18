<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\ExpiditingServicePriceConfigRequest;
use Modules\Core\Http\Requests\VehicleRegistrationConfigRequest;
use Modules\Core\Services\ConfigurationService;

class CoreController extends Controller
{
    protected $configService;
    public function __construct(ConfigurationService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->configService->getCountries();
    }

    /**
     * Display a listing of the resource.
     */
    public function counties()
    {
        return $this->configService->getCounties();
    }

    /**
     * Display a listing of the resource.
     */
    public function industries()
    {
        return $this->configService->industries();
    }




}
