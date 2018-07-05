<?php

namespace App\Domain\Accounts\Verification;

class VerifyCodeCreatedEvent
{
    /**
     * @var VerifyCode
     */
    public $verifyCode;

    /**
     * Creates a new VerifyCodeCreatedEvent
     *
     * @param VerifyCode  $verifyCode
     * @return void
     */
    public function __construct(VerifyCode $verifyCode)
    {
        $this->verifyCode = $verifyCode;
    }
}
