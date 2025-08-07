<?php

namespace Tests\Feature;

use App\Models\CreditPackage;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_payment_submission_and_admin_approval(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $pkg = CreditPackage::create(['name' => 'Test', 'credits' => 100, 'price' => 10.00, 'currency' => 'USD', 'is_active' => true]);

        $this->actingAs($user)
            ->post('/payments/manual', [
                'package_id' => $pkg->id,
                'reference' => 'BANKREF123',
            ])->assertRedirect('/dashboard');

        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);

        $this->actingAs($admin)
            ->post(route('admin.payments.approve', $payment))
            ->assertRedirect();

        $payment->refresh();
        $this->assertEquals('paid', $payment->status);
        $this->assertEquals(100 + $user->credits, $payment->user->fresh()->credits);
    }
}
