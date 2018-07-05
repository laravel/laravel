<?php

namespace Tests\Unit\App\Domain\Accounts\Verification;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

use App\Domain\Accounts\Account;
use App\Domain\Accounts\Verification\CreateVerifyCode;
use App\Domain\Accounts\Verification\VerifyCodeService;
use Tests\TestCase;

class CreateVerifyCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_GivenRegisteredEvent_VerifyCodeServiceCalled()
    {
        // Given
        $account = factory(Account::class)->create();

        // Then
        $mock = m::mock(VerifyCodeService::class);

        $mock->shouldReceive('createForAccount')
            ->with($account)
            ->once();

        $this->app->instance(VerifyCodeService::class, $mock);

        // When
        $this->app->make(CreateVerifyCode::class)->handle(new Registered($account));
    }
}
