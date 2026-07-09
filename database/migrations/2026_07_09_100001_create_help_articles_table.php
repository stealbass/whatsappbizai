<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('help_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->enum('type', ['article', 'tutorial', 'guide'])->default('article');

            // Translatable fields
            $table->string('title_fr');
            $table->string('title_en');
            $table->text('excerpt_fr')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_fr');
            $table->longText('content_en');

            // SEO
            $table->string('meta_title_fr')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->text('meta_description_fr')->nullable();
            $table->text('meta_description_en')->nullable();

            // Meta
            $table->string('featured_image')->nullable();
            $table->string('author_name')->default('WhatsAppBizAI');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('views')->default(0);

            // Interactive guide steps (JSON)
            $table->json('steps')->nullable();

            // Difficulty (for tutorials/guides)
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->integer('reading_minutes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_articles');
    }
};
