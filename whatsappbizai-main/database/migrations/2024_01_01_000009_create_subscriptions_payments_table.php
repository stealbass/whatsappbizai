<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des abonnements
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['free', 'starter', 'business', 'pro'])->default('free');
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('active');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('flutterwave_tx_ref')->nullable()->index();
            $table->string('flutterwave_tx_id')->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->string('currency', 10)->default('XAF');
            $table->json('features')->nullable();
            $table->timestamps();
        });

        // Table des paiements (Flutterwave + manuel)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['flutterwave', 'mtn_momo', 'orange_money', 'wave', 'bank_transfer', 'other']);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->enum('plan', ['starter', 'business', 'pro'])->default('starter');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('XAF');
            $table->string('reference')->nullable();                // Référence de transaction client
            $table->string('phone_number')->nullable();             // Numéro MoMo/Orange
            $table->string('screenshot_path')->nullable();          // Capture de preuve de paiement
            $table->text('notes')->nullable();                      // Notes du client
            $table->text('admin_notes')->nullable();                // Notes de l'admin
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
    }
};
