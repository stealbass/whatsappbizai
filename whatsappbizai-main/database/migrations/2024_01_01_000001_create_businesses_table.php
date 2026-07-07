<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('whatsapp_phone_number_id')->nullable();
            $table->text('whatsapp_access_token')->nullable();
            $table->string('whatsapp_business_account_id')->nullable();
            $table->text('gemini_system_prompt')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('CM');
            $table->string('currency', 10)->default('XAF');
            $table->string('logo_path')->nullable();
            $table->string('invoice_prefix', 20)->default('FAC');
            $table->string('quote_prefix', 20)->default('DEV');
            $table->boolean('is_active')->default(true);
            $table->enum('plan', ['free', 'starter', 'business', 'pro'])->default('free');
            $table->timestamp('plan_expires_at')->nullable();
            $table->string('timezone')->default('Africa/Douala');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
