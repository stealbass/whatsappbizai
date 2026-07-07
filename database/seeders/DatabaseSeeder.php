<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Contact;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Plans
        $this->call(PlanSeeder::class);
        // Business de démo
        $business = Business::create([
            'name'           => 'Tech Solutions Cameroun',
            'owner_name'     => 'Happi Olivier',
            'email'          => 'admin@whatsappbizai.com',
            'phone'          => '+237 6XX XXX XXX',
            'currency'       => 'XAF',
            'city'           => 'Douala',
            'country'        => 'CM',
            'invoice_prefix' => 'FAC',
            'quote_prefix'   => 'DEV',
            'timezone'       => 'Africa/Douala',
            'is_active'      => true,
            'plan'           => 'business',
            'gemini_system_prompt' => "Tu es l'assistant IA de Tech Solutions Cameroun. Spécialisés en développement web, applications mobiles et conseil digital. Nos délais de livraison sont de 2 à 6 semaines selon le projet.",
        ]);

        // Utilisateur admin (super-admin)
        User::create([
            'business_id'    => $business->id,
            'name'           => 'Happi Olivier',
            'email'          => 'admin@whatsappbizai.com',
            'password'       => Hash::make('password'),
            'role'           => 'admin',
            'is_super_admin' => true,
            'is_active'      => true,
        ]);

        // Services de démo
        $services = [
            ['name' => 'Site web vitrine',       'unit_price' => 250000,  'unit' => 'forfait', 'description' => 'Site web responsive 5 pages'],
            ['name' => 'Application mobile',     'unit_price' => 800000,  'unit' => 'forfait', 'description' => 'App Android/iOS sur mesure'],
            ['name' => 'E-commerce',             'unit_price' => 450000,  'unit' => 'forfait', 'description' => 'Boutique en ligne complète'],
            ['name' => 'Conseil & formation',    'unit_price' => 25000,   'unit' => 'heure',   'description' => 'Conseil technique et formation'],
            ['name' => 'Maintenance mensuelle',  'unit_price' => 30000,   'unit' => 'mois',    'description' => 'Maintenance et support mensuel'],
            ['name' => 'Développement sur mesure','unit_price' => 15000,  'unit' => 'heure',   'description' => 'Dev frontend/backend'],
        ];

        foreach ($services as $s) {
            Service::create(array_merge($s, [
                'business_id' => $business->id,
                'currency'    => 'XAF',
                'is_active'   => true,
            ]));
        }

        // Contacts de démo
        $contacts = [
            ['name' => 'Jean-Pierre Mbarga',  'whatsapp_number' => '+237611111111', 'status' => 'client'],
            ['name' => 'Marie Ngo',           'whatsapp_number' => '+237622222222', 'status' => 'prospect'],
            ['name' => 'Société ABC SARL',    'whatsapp_number' => '+237633333333', 'status' => 'client'],
        ];

        foreach ($contacts as $c) {
            Contact::create(array_merge($c, ['business_id' => $business->id]));
        }

        $this->command->info('✅ Données de démo créées. Login: admin@whatsappbizai.com / password');
    }
}
