<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('whatsapp_number', 30)->index();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['prospect', 'client', 'inactif'])->default('prospect');
            $table->timestamp('last_seen_at')->nullable();
            $table->decimal('total_invoiced', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'whatsapp_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
