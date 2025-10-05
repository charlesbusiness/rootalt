<?php

namespace Modules\Authentication\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Authentication\Dto\UserDto;
use Modules\Authentication\Http\Requests\ResendEmailVeificationRequest;
use Modules\Authentication\Http\Requests\VerifyEmailRequest;

use Modules\Core\Models\BusinessRole;
use Modules\Core\Services\ConfigurationService;
use Modules\Core\Services\CoreService;
use Modules\Core\Services\MessageService;
use Modules\Referral\Jobs\ReferralCodeJob;
use PragmaRX\Google2FALaravel\Google2FA;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class AuthenticationService extends CoreService
{
    protected $userModel;
    protected $auth;
    protected $emailService;
    protected $configService;
    protected $buznessRole;

    public function __construct(
        User $user,
        Auth $auth,
        MessageService $emailService,
        ConfigurationService $configService,
        BusinessRole $buznessRole
    ) {
        $this->userModel = $user;
        $this->auth = $auth;
        $this->emailService = $emailService;
        $this->buznessRole = $buznessRole;

        $this->configService = $configService;

        parent::__construct();
    }

    /** **
     * Grant a user access to user be generating a token
     ** */
    public function login($request)
    {
        try {
            $credentials = $request->validated();
            $token = null;

            $user = user($request->email ?? $request->username);
            logData($user);
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return failedResponse(null, "Invalid login credentials provided", Response::HTTP_BAD_REQUEST);
            }

            // Check if user has 2FA enabled
            if ($user->two_fa_status) {
                $codeData = $this->saveVerificationCode(type: $this->twoFaType, request: $request);
                $this->message = 'Please login into your authentication to complete you login';
                return successfulResponse($codeData, $this->message);
            }

            // dd($user);
            $token = $this->generateToken(
                user: $user
            );

            $this->message = "Logged in successfully";
            return successfulResponse($token, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }


    public function verify2FA($request)
    {

        $codeData = $this->getValidCode($request->all());

        $user = user($codeData->email);
        if (!$codeData || !$user->two_factor_secret) {
            return failedResponse(null, "Invalid 2FA token provided", 400);
        }

        // Verify the OTP
        $google2fa = new Google2FA($request);
        if (!$google2fa->verifyKey($user->two_factor_secret, $request->otp)) {
            return failedResponse(null, "Invalid 2fa token provided", 403);
        }

        // Revoke the temporary token and issue a new API token
        $token = $this->generateToken(user: $user);

        $this->message = "Logged in successfully";
        return successfulResponse($token, $this->message);
    }

    /**
     * Create an account for a vendor and return the data
     ** */
    public function register(Request $request)
    {

        $dto = UserDto::fromArray($request->all());
        try {

            DB::beginTransaction();

            $userData = $this->userModel->createUser($dto);
            $user = $userData['user'];

            if ($user) {
                $message = $this->message = "User created";
                $this->emailService->sendVerificationEmail(
                    request: $request,
                    user: $user,
                    template: $this->verificationTemplate,
                    type: $this->verificationType
                );

                $response = successfulResponse($user, $message, Response::HTTP_OK);
                DB::commit();

                ReferralCodeJob::dispatch($userData);
            } else {
                $message = $this->message = "User was not created";
                $response = failedResponse($user, $message, Response::HTTP_BAD_REQUEST);
                DB::rollBack();
            }
        } catch (Throwable $e) {

            DB::rollBack();
            $response = failedResponse(null, $this->errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
            logError($e);
        }
        return $response;
    }

    /**
     * Create an account for a vendor and return the data
     ** */
    public function verifyAccount(VerifyEmailRequest $request)
    {
        $validatedData = $request->validated();
        $user = null;
        try {
            $code = $this->getValidCode($validatedData);
            if ($code) {

                updateModel(
                    column: 'email',
                    values: $code->email,
                    data: ['email_verified_at' => now()],
                    model: $this->userModel
                );

                $message = $this->message = "Account Verified";

                $response = successfulResponse($user, $message, Response::HTTP_OK);
            } else {
                $message = $this->message = "Account not verified. Seems code has expired";
                $response = failedResponse($code, $message, Response::HTTP_OK);
            }
        } catch (Throwable $e) {
            $response = failedResponse($user, $this->errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
            logError($e);
        }
        return $response;
    }

    /**
     * To help get users that are not the main business owners
     */
    public function isNotBusinessUser($code)
    {

        $user = $this->userModel->query()
            ->where('email', $code['email'])
            ->where('is_business_owner', false)
            ->with('employee')
            ->first();
        // If this is not a business owner, they will need to have a default password generated and sent to them
        if ($user) {
            $user->name = $user->firstname . ' ' . $user->lastname;
            $password = $user->firstname . '@' . generateVerificationCode();

            $this->emailService->sendEmail(
                user: $user,
                template: $this->defaultPasswordTemplate,
                extra: $password
            );

            $user->update(
                [
                    'email_verified_at' => now(),
                    'password' => Hash::make($password)
                ]
            );
        }
        return $user;
    }

    public function resendVerificationCode(ResendEmailVeificationRequest $request)
    {
        $validatedData = $request->validated();

        $email = isset($validatedData['email']) ? $validatedData['email'] : $validatedData['phone'];

        $user = user($email);
        if ($user) {
            $this->emailService->sendVerificationEmail(
                $request,
                $user,
                $this->verificationTemplate,
                $this->verificationType
            );
            return successfulResponse($user, 'Verification code sent. Please check your inbox');
        } else $this->message = "No user was found with the provided data";
        return failedResponse($user, $this->message, 200);
    }
}
