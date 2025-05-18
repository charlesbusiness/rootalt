<?php

namespace Modules\Authentication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\Authentication\Emails\AccountVerificationEmail;
use Throwable;

class AccountVerificationJob implements ShouldQueue
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
            info("Email Data", [$this->data]);
            Mail::to($this->data['email'])->send(new AccountVerificationEmail($this->data));
            info("Email sent successfully to: " . $this->data['email']);
        } catch (\Exception $e) {
            logError($e);
            info("Email sending failed for: " . $this->data['email']);
        }
    }
}
