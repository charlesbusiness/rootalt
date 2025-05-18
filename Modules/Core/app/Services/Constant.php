<?php

namespace Modules\Core\Services;

class Constant
{
    protected $verificationTemplate = 'authentication::emails.verification';
    protected $defaultPasswordTemplate = 'authentication::emails.default-password';
    protected $phoneLoginTemplate = 'authentication::emails.phone-login';
    protected $errorMessage = "There was an error. Please contact Admin";

    protected $is_failed_response = false;
    protected $is_success_response = true;
    protected $verificationModel;
    protected $verificationType = 'verification';
    protected $defaultPasswordType = 'default-password';
    protected $resetPasswordType = 'reset-password';
    protected $googleAuthCodeType = 'google-auth';
    protected $phoneLoginType = 'phone-login';
    protected $twoFaType = '2fa-type';
    protected $frontendUrl;
    protected $tipmeOption = 'tipme';
    protected $taskStatustemplate = 'task::email.task-status';

    public function getTaskStatusTemplate(): string
    {
        return $this->taskStatustemplate;
    }
}
