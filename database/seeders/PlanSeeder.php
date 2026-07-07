<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug'          => 'free',
                'name'          => 'Free',
                'description'   => 'Pour démarrer et découvrir la plateforme',
                'price_monthly' => 0,
                'price_yearly'  => 0,
                'currency'      => 'XAF',
                'max_contacts'  => 50,
                'max_invoices'  => 10,
                'max_messages'  => 100,
                'features'      => ['whatsapp_integration', 'basic_invoicing', 'ai_assistant'],
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 0,
            ],
            [
                'slug'          => 'starter',
                'name'          => 'Starter',
                'description'   => 'Pour les petites entreprises en croissance',
                'price_monthly' => 9900,
                'price_yearly'  => 99000,
                'currency'      => 'XAF',
                'max_contacts'  => 500,
                'max_invoices'  => 100,
                'max_messages'  => 1000,
                'features'      => ['whatsapp_integration', 'invoicing', 'ai_assistant', 'contacts_crm', 'quotes'],
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 1,
            ],
            [
                'slug'          => 'business',
                'name'          => 'Business',
                'description'   => 'Pour les entreprises établies',
                'price_monthly' => 24900,
                'price_yearly'  => 249000,
                'currency'      => 'XAF',
                'max_contacts'  => 2000,
                'max_invoices'  => 500,
                'max_messages'  => 5000,
                'features'      => ['whatsapp_integration', 'invoicing', 'ai_assistant', 'contacts_crm', 'quotes', 'broadcast', 'retention_campaigns', 'multi_agent'],
                'is_active'     => true,
                'is_featured'   => true,
                'sort_order'    => 2,
            ],
            [
                'slug'          => 'pro',
                'name'          => 'Pro',
                'description'   => 'Pour les entreprises à fort volume',
                'price_monthly' => 49900,
                'price_yearly'  => 499000,
                'currency'      => 'XAF',
                'max_contacts'  => -1,
                'max_invoices'  => -1,
                'max_messages'  => -1,
                'features'      => ['whatsapp_integration', 'invoicing', 'ai_assistant', 'contacts_crm', 'quotes', 'broadcast', 'retention_campaigns', 'multi_agent', 'priority_support', 'custom_ai_training', 'api_access'],
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
