<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sandbox_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('to');                          // numéro destination
            $table->string('contact_name')->nullable();    // nom résolu depuis contacts
            $table->enum('type', ['text', 'document'])->default('text');
            $table->text('content')->nullable();           // texte ou nom du fichier
            $table->string('media_url')->nullable();
            $table->string('trigger')->nullable();         // reminder|broadcast|reply|invoice|quote
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sandbox_messages');
    }
};
