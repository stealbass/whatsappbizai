<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->longText('custom_head_css')->nullable()->after('stats_countries');
            $table->longText('custom_head_js')->nullable()->after('custom_head_css');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['custom_head_css', 'custom_head_js']);
        });
    }
};
