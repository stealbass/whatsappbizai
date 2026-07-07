<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Site Settings: add _fr/_en for translatable fields ────────────
        $siteFields = [
            'site_name', 'site_tagline', 'meta_title', 'meta_description',
            'meta_keywords', 'footer_description', 'footer_copyright',
            'privacy_policy', 'terms_conditions', 'cookie_policy',
        ];

        Schema::table('site_settings', function (Blueprint $table) use ($siteFields) {
            foreach ($siteFields as $field) {
                $table->text($field . '_fr')->nullable()->after($field);
                $table->text($field . '_en')->nullable()->after($field . '_fr');
            }
        });

        // Migrate existing data to _fr columns
        $row = DB::table('site_settings')->first();
        if ($row) {
            $updates = [];
            foreach ($siteFields as $field) {
                if (!empty($row->$field)) {
                    $updates[$field . '_fr'] = $row->$field;
                }
            }
            if ($updates) {
                DB::table('site_settings')->where('id', $row->id)->update($updates);
            }
        }

        // ─── Posts: add _fr/_en for translatable fields ────────────────────
        $postFields = ['title', 'excerpt', 'content', 'meta_title', 'meta_description'];

        Schema::table('posts', function (Blueprint $table) use ($postFields) {
            foreach ($postFields as $field) {
                $type = in_array($field, ['content']) ? 'longText' : 'text';
                $table->$type($field . '_fr')->nullable()->after($field);
                $table->$type($field . '_en')->nullable()->after($field . '_fr');
            }
        });

        // Migrate existing post data to _fr columns
        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            $updates = [];
            foreach ($postFields as $field) {
                if (!empty($post->$field)) {
                    $updates[$field . '_fr'] = $post->$field;
                }
            }
            if ($updates) {
                DB::table('posts')->where('id', $post->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        $siteFields = [
            'site_name', 'site_tagline', 'meta_title', 'meta_description',
            'meta_keywords', 'footer_description', 'footer_copyright',
            'privacy_policy', 'terms_conditions', 'cookie_policy',
        ];

        Schema::table('site_settings', function (Blueprint $table) use ($siteFields) {
            foreach ($siteFields as $field) {
                $table->dropColumn([$field . '_fr', $field . '_en']);
            }
        });

        $postFields = ['title', 'excerpt', 'content', 'meta_title', 'meta_description'];

        Schema::table('posts', function (Blueprint $table) use ($postFields) {
            foreach ($postFields as $field) {
                $table->dropColumn([$field . '_fr', $field . '_en']);
            }
        });
    }
};
