<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->constrained()->cascadeOnDelete();
                $table->string('plan');
                $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('pending');
                $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->string('flutterwave_tx_ref')->nullable();
                $table->string('flutterwave_tx_id')->nullable();
                $table->decimal('amount_paid', 12, 2)->default(0);
                $table->string('currency', 10)->default('XAF');
                $table->json('features')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
