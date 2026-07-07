<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'portal_token')) {
                $table->string('portal_token', 64)->nullable()->unique()->after('notes');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'plan')) {
                $table->enum('plan', ['free', 'starter', 'business', 'pro'])->default('free')->after('timezone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('portal_token');
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }
};
