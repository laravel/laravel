<?php

namespace App\Domain\Accounts\Verification;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Domain\Accounts\Account;
use App\Domain\Translations\Translations;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * ID of the Verify Code used to later retrieve the Code
     *
     * @var int
     */
    public $verify_code_id;

    /**
     * Creates an instance of the VerifyMail
     *
     * @param VerifyCode  $verifyCode
     * @return void
     */
    public function __construct(VerifyCode $verifyCode)
    {
        $this->verify_code_id = $verifyCode->id;
    }

    /**
     * Prepares the mailable to be sent.
     *
     * @return void
     */
    public function build()
    {
        $verifyCode = VerifyCode::findOrFail($this->verify_code_id);

        $this->subject(trans('accounts.verification.email_subject'));

        $this->view('mail.opt-in', [
            'model' => [
                'url' => route('verify-codes.show', $verifyCode->code),
                'text' => trans('accounts.verification.email_link'),
            ],
        ]);
    }
}
