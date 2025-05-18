<?php

namespace Modules\UserProfile\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\PaymentOption;
use Modules\Core\Services\CoreService;
use Modules\Core\Services\MessageService;
use Modules\UserProfile\Dto\LiberiaTipmeDto;
use Modules\UserProfile\Dto\MastercardDto;
use Modules\UserProfile\Dto\MobileMoneyDto;
use Modules\UserProfile\Models\LiberiaTipmePaymentOption;
use Modules\UserProfile\Models\MastercardPaymentOption;
use Modules\UserProfile\Models\MobileMoneyPaymentOption;
use Modules\UserProfile\Models\NotificationSetup;
use Modules\UserProfile\Models\RegisteredPaymentOption;
use Throwable;

class NotificationSetupService extends CoreService
{

    protected $option;
    protected $notificationSetup;
    protected $mobileMoney;
    protected $ltipme;
    protected $regOptions;
    protected $emailService;

    public function __construct(
        NotificationSetup $notificationSetup,
    ) {

        $this->notificationSetup = $notificationSetup;

        parent::__construct();
    }
    /**
     * Add a new payment option.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function createNotification(Request $request)
    {
        $request->validate([
            'notification_type' => ['required', 'string', 'in:email,sms']
        ]);
        $user = $request->user();
        try {

           $notification = $this->notificationSetup->updateOrCreate([
           'notification_type' => $request->notification_type,
           'user_id' => $user->id
           ]);

            if ($notification) {
                $this->message = "Notification added successfully";
                
                return successfulResponse($notification, $this->message);
            }

            return failedResponse();
        } catch (Throwable $th) {

            logError($th);
        }
    }


}
