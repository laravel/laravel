<?php

namespace Tests\Unit\App\Domain\Accounts\Verification;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Domain\Accounts\Account;
use App\Domain\Accounts\Verification\Events\VerifyCodeCreated;
use App\Domain\Accounts\Verification\Events\AccountVerified;
use App\Domain\Accounts\Verification\VerifyCode;
use App\Domain\Accounts\Verification\VerifyCodeService;
use App\Domain\Accounts\Verification\VerifyCodeNotification;

class VerifyCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        $this->service = $this->app->make(VerifyCodeService::class);
    }

    public function test_make_CreatesCodeForAccount()
    {
        // Given
        $account = factory(Account::class)->create();

        // When
        $code = $this->service->create($account);

        // Then
        $this->assertEquals($account->id, VerifyCode::find($code->id)->account->id);
    }

    public function test_make_GivenAnotherCodeAlreadyExists_OldCodesAreDeleted()
    {
        // Given
        $account = factory(Account::class)->create();
        $oldCode = $this->service->create($account);

        // When
        $newCode = $this->service->create($account);

        // Then
        $this->assertEquals([ $newCode->id ], VerifyCode::query()->pluck('id')->all());
        $this->assertEquals([ $oldCode->id ], VerifyCode::query()->onlyTrashed()->pluck('id')->all());
    }

    public function test_make_NotificationIsSent()
    {
        Notification::fake();

        // Given
        $account = factory(Account::class)->create();

        // When
        $this->service->create($account);

        // Then
        Notification::assertSentTo($account, VerifyCodeNotification::class);
    }

    public function test_make_EventIsDispatched()
    {
        Event::fake();

        // Given
        $account = factory(Account::class)->create();

        // When
        $this->service->create($account);

        // Then
        Event::assertDispatched(VerifyCodeCreated::class);
    }

    public function test_verify_CodeIsDeleted()
    {
        // Given
        $verifyCode = factory(VerifyCode::class)->create();

        // When
        $this->service->verify($verifyCode->code, 'secret');

        // Then
        $this->assertNull(VerifyCode::find($verifyCode->id));
    }

    public function test_verify_AccountIsVerified()
    {
        // Given
        $verifyCode = factory(VerifyCode::class)->create();

        // When
        $this->service->verify($verifyCode->code, 'secret');

        // Then
        $this->assertNotNull($verifyCode->account->verified_at);
    }

    public function test_verify_EventIsDispatched()
    {
        Event::fake();

        // Given
        $verifyCode = factory(VerifyCode::class)->create();

        // When
        $this->service->verify($verifyCode->code, 'secret');

        // Then
        Event::assertDispatched(AccountVerified::class);
    }
}
