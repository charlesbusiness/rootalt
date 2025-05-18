<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authentication\Http\Requests\ResendEmailVeificationRequest;
use Modules\Authentication\Http\Requests\ResetPasswordRequest;
use Modules\Authentication\Http\Requests\VerifyPasswordOtpRequest;
use Modules\Authentication\Services\AccountRecoveryService;

class AccountRecoveryController extends Controller
{
    protected $accountService;
    public function __construct(AccountRecoveryService $accountService)
    {
        $this->accountService = $accountService;
    }
    /**
     * Send an email to the user containing the account reset code.
     */
    public function sendPasswordResetEmail(ResendEmailVeificationRequest $request)
    {
        return $this->accountService->sendPasswordResetCode($request);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->accountService->resetPassword($request);
    }

    public function verifyPasswordOtp(VerifyPasswordOtpRequest $request)
    {
        return $this->accountService->verifyPasswordOtp($request);
    }
}
