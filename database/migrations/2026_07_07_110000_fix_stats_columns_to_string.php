<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('stats_users', 50)->default('< 30s')->change();
            $table->string('stats_invoices', 50)->default('24/7')->change();
            $table->string('stats_messages', 50)->default('FR + EN')->change();
            $table->string('stats_countries', 50)->default('100%')->change();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->integer('stats_users')->default(0)->change();
            $table->integer('stats_invoices')->default(0)->change();
            $table->integer('stats_messages')->default(0)->change();
            $table->string('stats_countries')->default('15+')->change();
        });
    }
};
