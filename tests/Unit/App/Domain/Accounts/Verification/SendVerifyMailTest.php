<?php

namespace Tests\Unit\App\Domain\Accounts\Verification;

use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

use App\Domain\Accounts\Verification\VerifyCode;
use App\Domain\Accounts\Verification\VerifyMail;
use App\Domain\Accounts\Verification\SendVerifyMail;
use App\Domain\Accounts\Verification\VerifyCodeCreatedEvent;
use Tests\TestCase;

class SendVerifyMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_MailableIsSent()
    {
        Mail::fake();

        // Given
        $code = factory(VerifyCode::class)->create();

        // When
        $this->app->make(SendVerifyMail::class)->handle(new VerifyCodeCreatedEvent($code));

        // Then
        Mail::assertSent(VerifyMail::class, function ($mail) use ($code) {
            return $mail->verify_code_id === $code->id;
        });
    }
}
