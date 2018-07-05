<?php

namespace Tests\Unit\App\Domain\Accounts\Verification;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Domain\Accounts\Account;
use App\Domain\Accounts\AccountCreatedEvent;
use App\Domain\Accounts\Verification\VerifyCode;
use App\Domain\Accounts\Verification\VerifyCodeService;

class VerifyCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        $this->service = $this->app->make(VerifyCodeService::class);
    }

    public function test_createForAccount_CreatesCodeForAccount()
    {
        // Given
        $account = factory(Account::class)->create();

        // When
        $code = $this->service->createForAccount($account);

        // Then
        $this->assertEquals($account->id, VerifyCode::find($code->id)->account->id);
    }

    public function test_createForAccount_GivenAnotherCodeAlreadyExists_OldCodesAreDeleted()
    {
        // Given
        $account = factory(Account::class)->create();
        $oldCode = $this->service->createForAccount($account);

        // When
        $newCode = $this->service->createForAccount($account);

        // Then
        $this->assertEquals([ $newCode->id ], VerifyCode::query()->pluck('id')->all());
        $this->assertEquals([ $oldCode->id ], VerifyCode::query()->onlyTrashed()->pluck('id')->all());
    }
}
