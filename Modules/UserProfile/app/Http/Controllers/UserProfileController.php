<?php

namespace Modules\UserProfile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UserProfile\Http\Requests\UpdateUserPasswordRequest;
use Modules\UserProfile\Http\Requests\UpdateUserProfileRequest;
use Modules\UserProfile\Services\ProfileSettingService;

class UserProfileController extends Controller
{

    protected $profileServices;
    public function __construct(ProfileSettingService $profileServices)
    {
        $this->profileServices = $profileServices;
    }


    /**
     * Update user personal data.
     */
    public function updatePersonalDetails(UpdateUserProfileRequest $request)
    {
        return $this->profileServices->updatePersonalDetails($request);
    }


    /**
     * Get The logged in user data .
     */
    public function getProfileData(Request $request)
    {
        return $this->profileServices->getProfileData($request);
    }

    /**
     * Update user personal data.
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        return $this->profileServices->updatePassord($request);
    }


    /**
     * Update user personal data.
     */
    public function enable2FA(Request $request)
    {
        return $this->profileServices->enable2FA($request);
    }

    /**
     * Update user personal data.
     */
    public function disable2FA(Request $request)
    {
        return $this->profileServices->disable2FA($request);
    }

    /**
     * Update user personal data.
     */
    public function uploadProfilePicture(Request $request)
    {
        return $this->profileServices->uploadProfilePicture($request);
    }

    /**
     * Update user personal data.
     */
    public function viewQRCode(Request $request)
    {   $user = $request->user();
        return view('codes', compact('user'));
    }

}
