<?php

namespace Modules\Authentication\Services;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use Modules\Core\Services\CoreService;
use Modules\Core\Services\MessageService;
use Throwable;

class AccountRecoveryService extends CoreService
{
    protected $emailService;
    protected $userModel;
    protected $resetPasswordTemplate = 'authentication::emails.reset-password';
    public function __construct(MessageService $emailService, User $userModel)
    {
        $this->emailService = $emailService;
        $this->userModel = $userModel;
        parent::__construct();
    }
    /**
     * Handle sending a reset password link to the user.
     *
     * @param  mixed  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPasswordResetCode($request)
    {
        $validatedData = $request->validated();
        $email = isset($validatedData['email']) ? $validatedData['email'] : $validatedData['phone'];

        $user = user($email);

        if ($user) {
            $request->merge(['email' => $user->email]);
            $this->emailService->sendVerificationEmail(
                request: $request,
                user: $user,
                template: $this->resetPasswordTemplate,
                type: $this->resetPasswordType
            );
            return successfulResponse($user, 'Password reset code sent. Please check your inbox');
        }
        $this->message = "No user was found with the provided data";
        return failedResponse($user, $this->message);
    }

    /**
     * Handle resetting the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyPasswordOtp($request)
    {
        try {

            $verificationCode = $this->getValidCode(['code' => $request->code]);
            if (is_null($verificationCode)) {
                $this->message = "Invalid reset password code provided";
                return failedResponse(null, $this->message, Response::HTTP_BAD_REQUEST);
            }
            $request->merge(['email' => $verificationCode->email]);
            $code = $this->saveVerificationCode($request, $this->resetPasswordType);
            $this->message = "Password reset token verified";
            return successfulResponse($code, $this->message, 200);
        } catch (Throwable $e) {
            logError($e);
            return failedResponse(null, $this->errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Handle resetting the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($request)
    {
        try {

            $code = $this->getValidCode(['code' => $request->code]);

            if (is_null($code)) {
                $this->message = "Invalid reset password code provided";
                return failedResponse(null, $this->message, Response::HTTP_BAD_REQUEST);
            }

            $hashPassword = Hash::make($request->password);
            $user = updateModel(
                data: ['password' => $hashPassword],
                model: $this->userModel,
                column: 'email',
                values: $code->email,
            );

            if (!$user) {
                $this->message = "Password could not be reset. Please retry";
                return failedResponse(null, $this->message);
            }
            $this->message = "Password reset";
            return successfulResponse($user, $this->message, 200);
        } catch (Throwable $e) {
            logError($e);
            return failedResponse(null, $this->errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
