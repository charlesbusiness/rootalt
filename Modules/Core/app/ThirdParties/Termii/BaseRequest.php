<?php

namespace Modules\Core\ThirdParties\Termii;

use Modules\Core\Http\Requests\HttpRequest;

class BaseRequest extends HttpRequest
{

  protected $baseUrl;
  protected $apiKey;
  protected $apiSecret;
  protected $smsFrom;
  protected $shouldStore=true;
  protected $bearerTokenIsRequired = false;
  protected $provider = 'TERMIL';



  public function __construct()
  {

    $this->baseUrl = config('core.termii.baseurl');
    $this->smsFrom = config('core.termii.sms_from');
    $this->apiKey = config('core.termii.api_key');
    $this->apiSecret = config('core.termii.api_secret');
   
  }
}
