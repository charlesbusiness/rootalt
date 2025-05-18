<?php

namespace Modules\Core\ThirdParties\Termii;

class SendSmsRequest extends BaseRequest
{
    protected $shouldStore = false;
    protected $url = '/api/sms/send';
    protected $totalRetry = 3;
    protected $retryDelay = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function sendSms(array $data)
    {
        $payload = [
            'to' => $data['to'],
            'from' => $this->smsFrom,
            'api_key' => $this->apiKey,
            'sms' => $data['sms'],
            'type' => 'plain',
            'channel' => 'generic',

        ];

        $response =  $this->withData($payload)->call();

        $response = (array) $response;

        if ($response['error'] == true) { //return from here when there is an error
            return $response;
        }

        $response =  $response['data'];

        return $response;
    }

    /** 
     *@inheritDoc
     */
    protected function MockResponse($data = null)
    {
        return json_encode([
            'message_id' => '9122821270554876574',
            'message' => 'Successfully Sent',
            'balance' => 10,
            'user' => 'Peter Mcleish'
        ], JSON_PRETTY_PRINT);
    }

    protected function shouldMock()
    {
        return false;
    }
}

//      "api_key": "Your API Key",
//      "media": {
//       "url": "https://media.example.com/file",
//       "caption": "your media file"
//   }    