<?php

namespace   Modules\Referral\Services;

use App\Models\User;
use Modules\Core\Services\CoreService;
use Modules\Referral\Models\ReferralCode;
use Modules\Referral\Models\UserReferral;

class ReferralService extends CoreService
{
    protected $referralCode;
    protected $userReferral;
    public function __construct(UserReferral $userReferral, ReferralCode $referralCode)
    {
        $this->referralCode = $referralCode;
        $this->userReferral = $userReferral;
    }

    public function generateReferralCode(User $user)
    {

        $code = generateReferralCode($user->username);
        $this->referralCode->create(['code' => $code, 'user_id' => $user->id]);
    }

    public function createReferrals($user, $code)
    {
        if ($code) {
            $referralCode = $this->referralCode->where('code', $code)->first();
            info("Referral code was provided");

            $this->userReferral->create([
                'referral_code_id' => $referralCode->id,
                'referral' => $referralCode->user_id,
                'referee' => $user->id
            ]);
        }
    }
}
