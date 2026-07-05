<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'site_tagline', 'logo_path', 'favicon_path',
        'meta_title', 'meta_description', 'meta_keywords', 'og_image_path', 'canonical_url',
        'contact_email', 'contact_phone', 'whatsapp_number',
        'facebook_url', 'twitter_url', 'linkedin_url', 'instagram_url', 'youtube_url',
        'privacy_policy', 'terms_conditions', 'cookie_policy',
        'footer_description', 'footer_copyright',
        'business_name', 'business_city', 'business_country', 'business_founding_date',
        'stats_users', 'stats_invoices', 'stats_messages', 'stats_countries',
    ];

    /**
     * Get the single site settings record (cached).
     * Uses ID=1 as the singleton row.
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
            'site_tagline' => "Back-office intelligent pour PME — Devis, factures et relances via WhatsApp grâce à l'IA",
            'meta_title' => 'WhatsAppBizAI — Agent IA WhatsApp pour PME | Devis, Factures, CRM',
            'meta_description' => "Automatisez votre back-office avec un agent IA sur WhatsApp. Devis instantanés, facturation, relances automatiques et CRM pour PME en Afrique. Essai gratuit.",
            'meta_keywords' => 'agent IA WhatsApp, devis automatique, facturation PME, CRM WhatsApp, relance automatique, back-office intelligent, SaaS Afrique, WhatsApp business, invoice automation, AI assistant',
            'contact_email' => 'contact@whatsappbizai.com',
            'business_name' => 'WhatsAppBizAI',
            'business_city' => 'Douala',
            'business_country' => 'CM',
            'business_founding_date' => '2026-01-01',
            'footer_copyright' => '© ' . date('Y') . ' WhatsAppBizAI. Tous droits réservés.',
        ];
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
