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

        $translations = trans('accounts.verification');

        $this->subject($translations['email_subject']);

        $this->view('mail.verify-code', [
            'model' => [
                'url' => route('verify-codes.show', $verifyCode->code),
                'title' => $translations['email_title'],
                'message' => $translations['email_message'],
                'button' => $translations['email_button'],
            ],
        ]);
    }
}
