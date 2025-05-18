<?php

namespace Modules\UserProfile\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Modules\Authentication\Models\TwoFa;
use Modules\Core\Models\Upload;
use Modules\Core\Services\CoreService;
use Modules\Core\Services\MessageService;
use PragmaRX\Google2FALaravel\Google2FA;
use Throwable;

class ProfileSettingService extends CoreService
{
    protected $emailService;
    protected $userModel;
    protected $twoFA;
    protected $upload;
    protected $resetPasswordTemplate = 'userprofile::emails.account-settings';
    public function __construct(MessageService $emailService, User $userModel,  TwoFa $twoFA, Upload $upload)
    {
        $this->emailService = $emailService;
        $this->userModel = $userModel;
        $this->twoFA = $twoFA;
        $this->upload = $upload;
        parent::__construct();
    }

    public function getProfileData(Request $request)
    {
        try {
            $user = $request->user();
            return successfulResponse($user);
        } catch (Throwable $e) {
            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }
    /**
     * Handle sending a reset password link to the user.
     *
     * @param  mixed  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePersonalDetails($request)
    {
        $validatedData = $request->validated();

        $user = $request->user();

        $updatedUser = updateModel(
            model: $this->userModel,
            column: 'email',
            values: $user->email,
            data: $validatedData
        );

        if ($updatedUser) {
            $this->emailService->sendEmail(
                request: $request,
                user: $user,
                template: $this->resetPasswordTemplate,
                type: $this->resetPasswordType
            );
            return successfulResponse($updatedUser, 'User profile updated successfully');
        } else {
            $this->message = "No user was found with the provided data";
            return failedResponse($user, $this->message, 200);
        }
    }

    /**
     * Handle resetting the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassord($request)
    {
        try {
            $password = $request->password;
            $user = $request->user();
            if (!Hash::check($request->old_password, $user->password)) {
                $this->message = "Invalid old password provided";
                return failedResponse(null, $this->message, Response::HTTP_BAD_REQUEST);
            }


            $hashPassword = Hash::make($password);

            $user = updateModel(
                data: ['password' => $hashPassword],
                model: $this->userModel,
                column: 'email',
                values: $user->email,
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


    public function enable2FA(Request $request)
    {

        $user = $request->user('sanctum');

        $google2fa = new Google2FA($request);
        $secretKey = $google2fa->generateSecretKey();

        $user->update([
            'two_factor_secret' => $secretKey,
            'two_factor_enabled_at' => now(),
            'two_fa_status' => 'on',
        ]);

        $qrcode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secretKey
        );

        $response = [
            'qr_code' => $qrcode,
            'secret' => $secretKey,
        ];

        file_put_contents(public_path('qrcodes/' . $user->id . '_qrcode.svg'), $qrcode);

        return successfulResponse($response);
    }

    public function disable2FA(Request $request)
    {

        $user = $request->user('sanctum');
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled_at' => null,
            'two_fa_status' => 'off',
        ]);
        $this->message = '2FA disabled';
        return successfulResponse($user, $this->message);
    }


    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'avater' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $type = 'profile_pictures';
            $user = $request->user('sanctum');

            $file = $request->file('avater');

            $url = $this->uploadImageToS3($file, $type);

            if ($url['res']) {

                if ($fileUpload = $this->upload
                    ->where('upload_owner', $user->id)
                    ->where('entity_id', $user->id)
                    ->first()
                ) {

                    $this->deleteFileFromS3($fileUpload->upload_path);
                    $fileUpload->update([
                        'upload_path' => $url['url'],
                        'file_name' => $url['fileName'],
                        'file_size' => $url['size'],
                    ]);
                    // $fileUpload = $fileUpload->fresh();
                } else {
                    $fileUpload = $this->upload->create([
                        'upload_owner' => $user->id,
                        'upload_type' => $type,
                        'upload_path' => $url['url'],
                        'file_size' => $url['size'],
                        'file_name' => $url['fileName'],
                        'entity_id' => $user->id,
                    ]);
                }

                $this->message = 'File uploaded';
                return successfulResponse($fileUpload, $this->message);
            }

            $this->message = 'File not uploaded';
            return failedResponse(null, $this->message);
        } catch (Throwable $e) {
            logError($e);
            return failedResponse(null);
        }
    }
}
