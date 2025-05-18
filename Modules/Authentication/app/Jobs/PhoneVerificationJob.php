<?php

namespace Modules\Authentication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\ThirdParties\Termii\SendSmsRequest;

class PhoneVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            info("SMS Data", [$this->data]);

            (new SendSmsRequest)->sendSms($this->data);
            info("SMS sent successfully to: " . $this->data['phone']);
        } catch (\Exception $e) {

            logError($e);
            info("SMS sending failed for: " . $this->data['phone']);
        }
    }
}
