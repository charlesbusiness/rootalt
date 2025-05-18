<?php

namespace Modules\Core\Http\Requests;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Core\Helpers\Helper;
use Modules\Core\Models\ApiRequest;
use Modules\Core\Models\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HttpRequest
{
  /**The request authorization token */
  protected $token;
  const CLIENT = "client";
  const SERVER = "server";
  const SUCCESS_CODE = '00';
  /** The duration to wait for an API call */
  protected $timeOut = 120;
  /** The timeout status code  */
  protected $timeOutCode = "0";
  /**The request base url */
  protected $baseUrl;
  protected $shouldMock = false;
  /**The request method */
  protected $method = 'post';
  /**The resource to send the request to */
  protected $url;
  /** The request tranfer protocole */
  protected $dataFormat = 'json';
  /** the site certificate verification option */
  protected $verify = true;
  /** The data to be sent to the api end point */
  protected $data = [];
  /**The request headers */
  protected $headers = [];
  protected $message = '';
  /** The request data */
  protected $request;
  /** Http status code */
  protected $statusCode;
  /** Transaction reference */
  protected $reference = null;

  /**  Service provider */
  protected $provider = null;

  /** Decide if APIs requests and responses should store */
  protected $shouldStore = false;

  /** Only endpoints with Bearer token authorization should have this set true */
  protected $bearerTokenIsRequired = true;
  /**
   * @inheritDoc
   * */
  public function withoutVerifying()
  {
    $this->verify = false;
    return $this;
  }

  public function withToken($token)
  {
    $this->token = $token;
    return $this;
  }

  public function request($request)
  {
    return $this->request = $request;
  }

  public function withBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
    return $this;
  }

  public function withMethod($method)
  {
    $this->method = $method;
    return $this;
  }

  public function withUrl($url)
  {
    $this->url = $url;
    return $this;
  }

  public function withReference($reference)
  {
    $this->reference = $reference;
    return $this;
  }

  /**
   * Build the URL to handle parameter dynamically
   * @param array $params the key value pair/associative of the url params
   * @return string
   */
  protected function withUrlBuilder(array $params)
  {
    $url = $this->url;
    foreach ($params as $key => $value) {
      $url = str_replace(":$key", $value, $url);
    }
    return  $url;
  }

  public function withData(array $data)
  {
    $this->data = $data;
    return $this;
  }

  public function withFormat($data)
  {
    $this->dataFormat = $data;
    return $this;
  }

  /** 
   *Determine whether a request should be mocked or not
   */
  protected function shouldMock()
  {
    return $this->shouldMock;
  }

  protected function builder()
  {
    $dataFormat = $this->dataFormat;
    if (!empty($this->data)) {
      return [$dataFormat => $this->data];
    }
    return [];
  }
  /**
   * Send an http request here and return a response for consumption on toher services
   */
  public function send()
  {
    $response = null;
    $apiRequest = null;
    try {

      $request = Http::withoutVerifying()
        ->timeout($this->timeOut)
        ->withHeaders($this->headers)
        ->baseUrl($this->baseUrl);
      if ($this->bearerTokenIsRequired) {
        $request =  $request->withToken($this->token);
      }

      // logData($request, true);
      $request = $request->asJson()
        ->send(
          $this->method,
          $this->url,
          $this->builder()
        );

      $apiRequest = $this->storeApiRequest(new Request);
      logData($this->data, $this->url, true);
      $requestStatus = $request->status();

      if ($request->successful()) {
        $data = $request->json() ?? $request->body();
        $response['data'] = isset($data['data']) ? $data['data'] : $data;

        $response['status'] = 1;
        $response['error'] = false;
        $response['message'] = 'success';
        $response['statusCode'] = $requestStatus;
      } else {
        $data = $request->json() ?? $request->body();
        $response['data'] = $data;

        $response['status'] = 2;
        $response['error'] = true;
        $response['statusCode'] = $requestStatus;

        logData($data, $this->url, false);
      }
      $this->statusCode = $requestStatus;
    } catch (ConnectionException $e) {
      $response['message'] = "There was a timeout from the provider. Please retry";

      $response['status'] = 3;
      $response['error'] = true;
      $response['data'] = null;
      $response['statusCode'] = $this->timeOutCode;
      $this->statusCode = 500;
      logError($e);
    } catch (Throwable | Exception | HttpResponseException $e) {
      $response['message'] = "Internal server error occured: Please contact admin ";

      $response['status'] = 3;
      $response['error'] = true;
      $response['data'] = null;
      $response['statusCode'] = Response::HTTP_INTERNAL_SERVER_ERROR;
      $this->statusCode = 500;
      logError($e);
    }

    logData($response, $this->url, false);
    $this->storeApiResponse($response, $apiRequest ? $apiRequest->id : null);

    $response['message'] = $this->message($response);
    return $response;
  }

  protected function isInvalidToken()
  {
    $token = $this->token;

    if (isset($token['error']) && $token['error'] == true) {
      return failedResponse($token, "Invalid token");
    }

    return false;
  }

  /**
   * Choose to either mock the response or send the request
   */
  public function call()
  {
    if ((Helper::isTest() or Helper::isDev()) and $this->shouldMock()) $response = $this->isTest();
    else $response =  $this->send();
    return $response;
  }


  /**
   *Set the appropraite http headers
   */
  public function withHeaders(array $headers)
  {
    $this->headers = $headers;
    return $this;
  }

  /**
   *Create mock of the http request instead of calling the api direct
   * @param $data
   *@return mixed
   */
  protected function MockResponse($data = null) {}

  /**
   * If environment is test, return the mocked data
   */
  protected function isTest()
  {
    $response = $this->MockResponse($this->request);
    if (gettype($response) == 'string') {
      $response = json_decode($response, true);
    }
    $data = null;
    $data['data'] = $response;
    $data['responseCode'] = '00';
    $data['message'] = 'success';
    $data['status'] = 1;
    $data['error'] = false;
    $data['statusCode'] = 200;
    return $data;
  }

  /**
   * Store the request in database.
   *
   * @param Request $request
   * @return mixed
   */
  protected function storeApiRequest()
  {
    if (!$this->shouldStore) return;

    $data = [
      'base_url' => $this->baseUrl,
      'endpoint' => $this->url,
      'request_data' => json_encode($this->data),
      'provider' => $this->provider,
    ];
    return ApiRequest::create($data);
  }

  /**
   * Store the data in storage.
   *
   * @return void
   */
  protected function storeApiResponse($data, $api_request_id = null): void
  {
    if (!$this->shouldStore) return;

    $data = [
      'api_request_id' => $api_request_id,
      'response_status_code' => $this->statusCode,
      'response_message' => $this->message,
      'response_data' => json_encode($data),
      'provider' => $this->provider,
      
    ];

    ApiResponse::create($data);
  }


  /**
   * Generate the HMAC signature based on the formatted data.
   *
   * @return string
   */
  public function getSignature()
  {
    $data = $this->data;
    $jsonEncodedBody = json_encode($data, JSON_UNESCAPED_SLASHES);
    return hash_hmac('sha256', $jsonEncodedBody, config('electricityprovider.vas.api_key'),);
  }



  protected function message($data)
  {
    
    $this->message = "External server error";
    if ($data && isset($data['status']) && $data['status'] != 1) {
      if (is_string($data)) {
        info("Data is a string");
        $data = json_decode($data, JSON_PRETTY_PRINT);
      }
      if (isset($data['message'])) {
        $this->message = $data['message'];
      }
      if (isset($data['data']['message'])) {
        $this->message = $data['data']['message'];
      }
      if (isset($data['serviceMessage'])) {
        $this->message = $data['serviceMessage'];
      }
      if (isset($data['data']['serviceMessage'])) {
        $this->message = $data['data']['serviceMessage'];
      }
    }
    return $this->message;
  }
}
