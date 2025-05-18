<?php

namespace Modules\Authentication\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class AccountVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
// 'authentication::emails.verification'
    /**
     * Build the message.
     */
    public function build()
    {
        try {
            return $this->view($this->data['template'])
                ->with([
                    'data' => $this->data,
                ]);
        } catch (Throwable $e) {
            logError($e);
        }
    }
}
