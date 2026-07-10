<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class SiteSetting extends Model
{
    protected $fillable = [
        // Base fields (non-translatable)
        'site_name', 'site_tagline', 'logo_path', 'favicon_path',
        'meta_title', 'meta_description', 'meta_keywords', 'og_image_path', 'canonical_url',
        'contact_email', 'contact_phone', 'whatsapp_number',
        'facebook_url', 'twitter_url', 'linkedin_url', 'instagram_url', 'youtube_url',
        'privacy_policy', 'terms_conditions', 'cookie_policy',
        'footer_description', 'footer_copyright',
        'business_name', 'business_city', 'business_country', 'business_founding_date',
        'stats_users', 'stats_invoices', 'stats_messages', 'stats_countries',
        'custom_head_css', 'custom_head_js',
        // Bilingual _fr/_en columns
        'site_name_fr', 'site_name_en',
        'site_tagline_fr', 'site_tagline_en',
        'meta_title_fr', 'meta_title_en',
        'meta_description_fr', 'meta_description_en',
        'meta_keywords_fr', 'meta_keywords_en',
        'footer_description_fr', 'footer_description_en',
        'footer_copyright_fr', 'footer_copyright_en',
        'privacy_policy_fr', 'privacy_policy_en',
        'terms_conditions_fr', 'terms_conditions_en',
        'cookie_policy_fr', 'cookie_policy_en',
    ];

    /**
     * Translatable fields that have _fr/_en columns.
     */
    const TRANSLATABLE = [
        'site_name', 'site_tagline', 'meta_title', 'meta_description',
        'meta_keywords', 'footer_description', 'footer_copyright',
        'privacy_policy', 'terms_conditions', 'cookie_policy',
    ];

    /**
     * Get the single site settings record (cached).
     */
    public static function instance(): self
    {
        return Cache::remember('site_settings', 3600, function () {
            return static::firstOrCreate(['id' => 1], self::defaults());
        });
    }

    /**
     * Refresh the cache after an update.
     */
    public static function refreshCache(): void
    {
        Cache::forget('site_settings');
    }

    /**
     * Default values for a fresh install.
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'WhatsAppBizAI',
            'site_name_fr' => 'WhatsAppBizAI',
            'site_name_en' => 'WhatsAppBizAI',
            'site_tagline_fr' => "Back-office intelligent pour PME — Devis, factures et relances via WhatsApp grâce à l'IA",
            'site_tagline_en' => 'Smart back-office for SMEs — Quotes, invoices and reminders via WhatsApp powered by AI',
            'meta_title_fr' => 'WhatsAppBizAI — Agent IA WhatsApp pour PME | Devis, Factures, CRM',
            'meta_title_en' => 'WhatsAppBizAI — AI WhatsApp Agent for SMEs | Quotes, Invoices, CRM',
            'meta_description_fr' => "Automatisez votre back-office avec un agent IA sur WhatsApp. Devis instantanés, facturation, relances automatiques et CRM pour PME en Afrique. Essai gratuit.",
            'meta_description_en' => 'Automate your back-office with an AI agent on WhatsApp. Instant quotes, invoicing, automatic reminders and CRM for SMEs in Africa. Free trial.',
            'meta_keywords_fr' => 'agent IA WhatsApp, devis automatique, facturation PME, CRM WhatsApp, relance automatique, back-office intelligent, SaaS Afrique',
            'meta_keywords_en' => 'AI WhatsApp assistant, automated quotes, SME invoicing, WhatsApp CRM, automatic reminders, smart back-office, Africa SaaS',
            'contact_email' => 'contact@whatsappbizai.com',
            'business_name' => 'WhatsAppBizAI',
            'business_city' => 'Douala',
            'business_country' => 'CM',
            'business_founding_date' => '2026-01-01',
            'footer_copyright_fr' => '© ' . date('Y') . ' WhatsAppBizAI. Tous droits réservés.',
            'footer_copyright_en' => '© ' . date('Y') . ' WhatsAppBizAI. All rights reserved.',
            'footer_description_fr' => '<p>Agent IA WhatsApp pour PME africaines. Devis, factures, relances et CRM automatisés — le tout depuis votre WhatsApp existant.</p>',
            'footer_description_en' => '<p>AI WhatsApp agent for African SMEs. Automated quotes, invoices, reminders and CRM — all from your existing WhatsApp.</p>',
        ];
    }

    /**
     * Get a translatable field for the current locale.
     * Falls back: _fr → _en → base field → null
     */
    public function trans(string $field): ?string
    {
        $locale = App::getLocale();
        $fr = $this->{$field . '_fr'};
        $en = $this->{$field . '_en'};
        $base = $this->{$field};

        return match ($locale) {
            'en' => $en ?? $fr ?? $base,
            default => $fr ?? $en ?? $base,
        };
    }

    /**
     * Override save to refresh cache.
     */
    public function save(array $options = []): bool
    {
        $result = parent::save($options);
        self::refreshCache();
        return $result;
    }
}
