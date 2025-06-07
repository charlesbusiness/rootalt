<?php

namespace Modules\Core\Services;

use Modules\Authentication\Jobs\AccountVerificationJob;
use Modules\Authentication\Jobs\PhoneVerificationJob;
use Modules\Authentication\Models\OtpManager;
use Modules\Core\Models\Country;
use Modules\Core\ThirdParties\Termii\SendSmsRequest;

class MessageService extends CoreService
{
    protected $message;
    protected $is_failed_response = false;
    protected $is_success_response = true;
    protected $verificationModel;
    protected $country;

    public function __construct(OtpManager $verificationModel, Country $country)
    {
        $this->verificationModel = $verificationModel;
        $this->country = $country;
    }



    /**
     * Array of data to processing
     */
    public function sendVerificationEmail($request, $user, string $template, string $type = null): void
    {
        $code = $this->saveVerificationCode($request, $type);
        $emailData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'verificationCode' => $code->code,
            'template' => $template
        ];
        AccountVerificationJob::dispatch($emailData);
    }

    /**
     * Array of data to processing
     */
    public function sendVerificationSms($request, $user, string $template, string $type = null, mixed $phoneCode): void
    {
        $code = $this->saveVerificationCode($request, $type);

        $internationalPhone = preg_replace('/^0/', $phoneCode, $user->phone);

        $smsData = [
            'user_id' => $user->id,
            'phone' => $internationalPhone,
            'to' => $internationalPhone,
            'name' => $user->name,
            'verificationCode' => $code->code,
            'sms' => $template
        ];

        (new SendSmsRequest)->sendSms($smsData);

        PhoneVerificationJob::dispatch($smsData);
    }


    /**
     * Array of data to processing
     */
    public function sendEmail($user, string $template,  $extra = null): void
    {

        $emailData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'template' => $template,
            'extra' => $extra,
        ];
        AccountVerificationJob::dispatch($emailData);
    }
}
