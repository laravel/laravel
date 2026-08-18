<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->string('name'); $t->string('code')->unique();
            $t->string('currency', 3)->default('AED'); $t->string('timezone')->default('Asia/Dubai');
            $t->string('locale')->default('en'); $t->time('delivery_cutoff_time')->default('23:59');
            $t->unsignedBigInteger('next_receipt_number')->default(1); $t->boolean('is_active')->default(true); $t->timestamps();
        });

        Schema::create('devices', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $t->string('name'); $t->string('device_key');
            $t->string('platform')->default('android'); $t->timestamp('approved_at')->nullable(); $t->timestamp('disabled_at')->nullable();
            $t->timestamp('last_seen_at')->nullable(); $t->timestamps(); $t->unique(['company_id','device_key']);
        });
        Schema::create('buildings', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('name'); $t->string('code'); $t->text('address')->nullable(); $t->string('contact_person')->nullable();
            $t->boolean('is_active')->default(true); $t->unsignedInteger('version')->default(1); $t->timestamps(); $t->unique(['company_id','code']);
        });
        Schema::create('rooms', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('building_id')->constrained()->cascadeOnDelete(); $t->string('number'); $t->string('floor')->nullable();
            $t->unsignedInteger('capacity')->default(1); $t->boolean('delivery_enabled')->default(true); $t->unsignedInteger('version')->default(1); $t->timestamps();
            $t->unique(['building_id','number']);
        });
        Schema::create('meal_plans', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('name'); $t->string('meal_option'); $t->string('billing_type');
            $t->decimal('daily_rate',12,2)->default(0); $t->decimal('monthly_rate',12,2)->default(0);
            $t->boolean('credit_pauses')->default(false); $t->boolean('credit_skips')->default(false); $t->boolean('is_active')->default(true);
            $t->unsignedInteger('version')->default(1); $t->timestamps();
        });
        Schema::create('customers', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('building_id')->constrained()->restrictOnDelete(); $t->foreignId('room_id')->constrained()->restrictOnDelete();
            $t->string('customer_code'); $t->string('name'); $t->string('passport_number'); $t->string('phone')->nullable();
            $t->date('start_date'); $t->decimal('opening_balance',12,2)->default(0); $t->boolean('is_active')->default(true);
            $t->unsignedInteger('version')->default(1); $t->timestamps(); $t->unique(['company_id','passport_number']); $t->unique(['company_id','customer_code']);
        });
        Schema::create('customer_plans', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('customer_id')->constrained()->cascadeOnDelete(); $t->foreignId('meal_plan_id')->constrained()->restrictOnDelete();
            $t->date('starts_on'); $t->date('ends_on')->nullable(); $t->decimal('rate_override',12,2)->nullable(); $t->timestamps();
        });
        Schema::create('meal_pauses', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->date('starts_on'); $t->date('resumes_on')->nullable(); $t->string('reason')->nullable(); $t->foreignId('created_by')->constrained('users'); $t->timestamps();
        });
        Schema::create('meal_skips', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->date('skip_date'); $t->string('meal_option'); $t->string('reason')->nullable(); $t->foreignId('created_by')->constrained('users'); $t->timestamps();
            $t->unique(['customer_id','skip_date','meal_option']);
        });
        Schema::create('deliveries', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('customer_id')->constrained()->restrictOnDelete();
            $t->date('delivery_date'); $t->string('meal_option'); $t->string('status'); $t->unsignedInteger('quantity')->default(1);
            $t->foreignId('recorded_by')->constrained('users'); $t->text('notes')->nullable(); $t->string('idempotency_key')->nullable();
            $t->unsignedInteger('version')->default(1); $t->timestamps(); $t->unique(['company_id','idempotency_key']);
            $t->unique(['customer_id','delivery_date','meal_option']);
        });
        Schema::create('invoices', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('customer_id')->constrained()->restrictOnDelete();
            $t->string('invoice_number'); $t->date('period_start'); $t->date('period_end'); $t->decimal('previous_balance',12,2)->default(0);
            $t->decimal('current_charges',12,2); $t->decimal('total_payable',12,2); $t->decimal('amount_paid',12,2)->default(0);
            $t->decimal('balance',12,2); $t->string('status')->default('unpaid'); $t->unsignedInteger('version')->default(1); $t->timestamps();
            $t->unique(['company_id','invoice_number']);
        });
        Schema::create('invoice_lines', function (Blueprint $t) {
            $t->id(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->string('description'); $t->decimal('quantity',10,2); $t->decimal('unit_price',12,2); $t->decimal('amount',12,2); $t->json('metadata')->nullable(); $t->timestamps();
        });
        Schema::create('payments', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('customer_id')->constrained()->restrictOnDelete();
            $t->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('employee_id')->constrained('users');
            $t->decimal('amount',12,2); $t->string('mode'); $t->timestamp('paid_at'); $t->string('reference')->nullable(); $t->text('notes')->nullable();
            $t->string('proof_path')->nullable(); $t->string('idempotency_key'); $t->foreignId('reverses_payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $t->string('correction_reason')->nullable(); $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
            $t->unique(['company_id','idempotency_key']);
        });
        Schema::create('ledger_entries', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('customer_id')->constrained()->restrictOnDelete();
            $t->string('type'); $t->decimal('debit',12,2)->default(0); $t->decimal('credit',12,2)->default(0); $t->decimal('running_balance',12,2);
            $t->nullableMorphs('source'); $t->text('description')->nullable(); $t->timestamps();
        });
        Schema::create('receipts', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('payment_id')->unique()->constrained()->restrictOnDelete();
            $t->string('receipt_number'); $t->string('pdf_path')->nullable(); $t->timestamp('shared_at')->nullable(); $t->foreignId('shared_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps(); $t->unique(['company_id','receipt_number']);
        });
        Schema::create('cash_handovers', function (Blueprint $t) {
            $t->id(); $t->uuid('uuid')->unique(); $t->foreignId('company_id')->constrained()->cascadeOnDelete(); $t->foreignId('employee_id')->constrained('users');
            $t->decimal('amount_submitted',12,2); $t->decimal('verified_amount',12,2)->nullable(); $t->decimal('variance',12,2)->nullable();
            $t->string('status')->default('not_submitted'); $t->timestamp('submitted_at')->nullable(); $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('verified_at')->nullable(); $t->text('notes')->nullable(); $t->string('proof_path')->nullable(); $t->string('idempotency_key'); $t->timestamps();
            $t->unique(['company_id','idempotency_key']);
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id(); $t->foreignId('company_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action'); $t->nullableMorphs('auditable'); $t->json('old_values')->nullable(); $t->json('new_values')->nullable();
            $t->string('ip_address',45)->nullable(); $t->text('user_agent')->nullable(); $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id(); $t->morphs('tokenable'); $t->string('name'); $t->string('token',64)->unique();
            $t->text('abilities')->nullable(); $t->timestamp('last_used_at')->nullable(); $t->timestamp('expires_at')->nullable()->index(); $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['personal_access_tokens','audit_logs','cash_handovers','receipts','ledger_entries','payments','invoice_lines','invoices','deliveries','meal_skips','meal_pauses','customer_plans','customers','meal_plans','rooms','buildings','devices'] as $table) Schema::dropIfExists($table);
        Schema::dropIfExists('companies');
    }
};
