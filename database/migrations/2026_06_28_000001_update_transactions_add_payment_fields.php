<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // payment_url = Tripay checkout_url sent to customer
            $table->string('payment_url', 500)->nullable()->after('reference');
            // pay_code = VA number / payment code (non-QRIS)
            $table->string('pay_code')->nullable()->after('payment_url');
            // expired_at = when payment window closes
            $table->timestamp('expired_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_url', 'pay_code', 'expired_at']);
        });
    }
};
