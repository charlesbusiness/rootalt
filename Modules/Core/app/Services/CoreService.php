<?php

namespace Modules\Core\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Authentication\Models\OtpManager;
use Modules\BusinessManager\Models\Business;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CoreService extends Constant
{
    protected $message;
    public const MAIN_BUSINESS_ADMIN = 'main-business-admin';


    public function __construct()
    {
        $this->verificationModel = (new OtpManager);
        $this->frontendUrl = config('core.frontend_url');
    }

    protected function fileUpload($request, $filename = 'file', $oldPath = null, $location = 'uploads')
    {
        if ($oldPath) {
            // Convert the URL path to a local file path
            $oldFilePath = public_path(parse_url($oldPath, PHP_URL_PATH));

            // Check if the file exists, then delete it
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $file = $request->file($filename);
        $filePath = $location;
        $fileName = time();
        $file->move(public_path($filePath), $fileName);
        $fullPath = config('core.app_url') . "/$filePath/$fileName";

        return $fullPath;
        //   return asset($filePath . '/' . $fileName);
    }


    protected function getValidCode($data)
    {

        $code = $this->verificationModel::query()
            ->where('code', $data['code'])
            ->where('status', 'active')
            ->where('expiration_time', '>=', now())
            ->first();

        if ($code) {
            $this->verificationModel::query()
                ->where('id', $code->id)
                ->delete();
        }

        return $code;
    }


    /** 
     * Generate a token for all devices.
     */
    protected function generateToken(string $email = null, Model $model = null, $user = null)
    {
        // Retrieve the user if not provided
        $user = $user ?? $model::query()->where('email', $email)->first();

        // Revoke all existing tokens for the user
        $user->tokens()->delete();


        // Create a personal access token for the user
        $token = $user->createToken(config('authentication.access_token'));

        // Assign token details to the user
        $tokenExpiration = $token->accessToken->expire_at ?? now()->addHours(5);

        $user->expire_at = $tokenExpiration; // Token expiration
        $user->token = $token->plainTextToken; // The actual token string

        return $user;
    }


    public function deleteToken($user, $tokenId)
    {
        $user->tokens()->where('id', $tokenId)->delete();
    }

    /** generate a token for all device */
    protected function generatePassportToken(?string $email, ?Model $model, $user = null)
    {
        $user = $user ?? $model::query()->where('email', $email)->first();

        $token = $user->createToken(config('authentication.access_token'));
        $user->expire_at = $token->token->expires_at->toDateTimeString();
        $user->token = $token->accessToken;
        return $user;
    }



    protected function saveVerificationCode($request, string $type)
    {
        $code = generateVerificationCode();
        $request->merge([
            'code' => $code,
            'expiration_time' => now()->addHours(5),
        ]);

        //Delete any related code that matches this email and type
        $this->verificationModel->query()->where([
            'email' => $request->email,
            'type' => $type,
        ])->delete();

        $codeData = $this->verificationModel->create(
            [
                'code' => $request->code,
                'expiration_time' => $request->expiration_time,
                'email' => $request->email,
                'type' => $type
            ]
        );
        return $codeData;
    }



    public function uploadImageToS3($file, string $folder)
    {
        // Generate a unique file name
        $fileName = "uploads/$folder/" . uniqid() . '.' . $file->getClientOriginalExtension();
        $fileOriginalName = $file->getClientOriginalName(); // Correct method
        $fileSize = $file->getSize();
        // Upload file to S3
        $res = Storage::disk('s3')->put($fileName, file_get_contents($file));

        /** @noinspection PhpUndefinedMethodInspection */
        $url = Storage::disk('s3')->url($fileName);

        return [
            'url' => $url,
            'res' => $res,
            'size' => round($fileSize / 1024, 2) . 'kb',
            'fileName' => $fileOriginalName
        ];
    }

    public function uploadMultipleFilesToS3($files, string $folder = 'uploads')
    {
        $uploadedFiles = [];

        // Ensure $files is an array (convert single file to array)
        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            // Generate a unique file name
            $fileFolder = isset($file['folder']) ? $file['folder'] : $folder;

            $fileName = "uploads/$fileFolder/" . uniqid() . '.' . $file['file']->getClientOriginalExtension();

            // Upload file to S3
            $res = Storage::disk('s3')->put($fileName, file_get_contents($file));

            // Get the URL if upload is successful
            if ($res) {
                $uploadedFiles[] = [
                    'url' => Storage::disk('s3')->url($fileName),
                    'res' => $res
                ];
            }
        }

        return  $uploadedFiles;
    }


    public function deleteFileFromS3($path)
    {
        $fileName = trim(parse_url($path, PHP_URL_PATH), '/');

        $result = Storage::disk('s3')->delete($fileName);

        $res = $result ? 'File deleted successfully' : 'Failed to delete file';
        logData($res);
    }

    protected function createOrUpdateMultiple(array $records, Model $model, array $uniqueKeys)
    {

        foreach ($records as $record) {
            $conditions = [];
            foreach ($uniqueKeys as $key) {
                $conditions[$key] = $record[$key];
            }
            $model->updateOrCreate(
                $conditions,
                $record // Fields to update or create
            );
        }
    }


    public function isAnEmployee($user)
    {
        return User::query()
            ->where('email', $user->email)
            ->where('is_business_owner', false)
            ->with('employee')
            ->first();
    }




    public function checkAccess($businessRole, array $data)
    {

        $defaultRole = $businessRole->whereName(self::MAIN_BUSINESS_ADMIN)
            ->first();

        $businessRoles = $businessRole->whereBusinessId(
            $this->loadBusinessData(request())->id
        )->pluck('id')
            ->toArray();

        throw_if(
            in_array($defaultRole->id, $data),
            new HttpException(400, 'A default role is not allowed to be assigned to an employee')
        );

        $invalidRoles = array_diff($data, $businessRoles);

        throw_if(
            !empty($invalidRoles),
            new HttpException(403, 'Only roles created by a business can be asigned to its employees')
        );
    }


    public function checkIfItIsPublicApi($resoucesModel, array $data)
    {
        $apiResources = $resoucesModel->whereIsPublic(true)->pluck('id')->toArray();

        $invalidApiResource = array_diff($data, $apiResources);

        throw_if(
            !empty($invalidApiResource),
            new HttpException(403, 'Only public api resources can be mapped')
        );
    }
}
