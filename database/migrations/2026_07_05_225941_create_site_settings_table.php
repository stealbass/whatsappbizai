<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Branding
            $table->string('site_name', 200)->default('WhatsAppBizAI');
            $table->string('site_tagline', 300)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('favicon_path', 500)->nullable();

            // SEO
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image_path', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();

            // Contact & Social
            $table->string('contact_email', 200)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('twitter_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();

            // Legal
            $table->text('privacy_policy')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('cookie_policy')->nullable();

            // Footer
            $table->text('footer_description')->nullable();
            $table->string('footer_copyright', 500)->nullable();

            // Business Info (for JSON-LD structured data)
            $table->string('business_name', 200)->nullable();
            $table->string('business_city', 100)->nullable();
            $table->string('business_country', 100)->nullable();
            $table->string('business_founding_date', 20)->nullable();

            // Social Proof
            $table->integer('stats_users')->default(0);
            $table->integer('stats_invoices')->default(0);
            $table->integer('stats_messages')->default(0);
            $table->string('stats_countries')->default('15+');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
