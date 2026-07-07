<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $s = SiteSetting::instance();

        // Legal content (from existing hardcoded pages)
        $s->update([
            'privacy_policy' => '<h2>1. Introduction</h2>
<p>WhatsAppBizAI respecte votre vie privée. Cette politique explique quelles données nous collectons, pourquoi, et comment vous pouvez exercer vos droits.</p>

<h2>2. Données collectées</h2>
<ul>
<li><strong>Compte :</strong> nom, nom d\'entreprise, email, ville, mot de passe (chiffré).</li>
<li><strong>WhatsApp Business :</strong> numéro de téléphone, token d\'API Meta.</li>
<li><strong>Données clients :</strong> contacts, conversations, devis et factures que vous gérez via l\'outil.</li>
<li><strong>Messages IA :</strong> historique des conversations avec l\'agent IA pour assurer la continuité du service.</li>
<li><strong>Paiements :</strong> identifiants de transaction Flutterwave. Nous ne stockons PAS vos données bancaires.</li>
</ul>

<h2>3. Utilisation des données</h2>
<p>Vos données sont utilisées uniquement pour :</p>
<ul>
<li>Fournir et améliorer nos services.</li>
<li>Envoyer les devis, factures et relances que vous configurez.</li>
<li>Assurer le support client.</li>
<li>Respecter nos obligations légales.</li>
</ul>

<h2>4. Partage des données</h2>
<p>Nous ne vendons jamais vos données. Elles ne sont partagées qu\'avec :</p>
<ul>
<li><strong>Meta/WhatsApp :</strong> pour l\'envoi des messages (API WhatsApp Business).</li>
<li><strong>Google Gemini :</strong> pour le traitement IA des conversations.</li>
<li><strong>Flutterwave :</strong> pour le traitement sécurisé des paiements.</li>
<li><strong>Hébergeur :</strong> serveurs sécurisés pour le stockage des données.</li>
</ul>

<h2>5. Sécurité</h2>
<p>Vos données sont protégées par chiffrement TLS/SSL en transit et au repos. Votre mot de passe est haché avec bcrypt. Nous appliquons les bonnes pratiques de sécurité OWASP.</p>

<h2>6. Conservation des données</h2>
<p>Les données de votre compte sont conservées tant que votre compte est actif. Après suppression, elles sont effacées sous 30 jours, sauf obligation légale contraire.</p>

<h2>7. Cookies</h2>
<p>Nous utilisons des cookies strictement nécessaires au fonctionnement du site (session, préférences de langue/devise). Aucun cookie publicitaire ou de tracking tiers n\'est utilisé.</p>

<h2>8. Vos droits</h2>
<p>Conformément au RGPD et aux lois locales, vous avez le droit de :</p>
<ul>
<li>Accéder à vos données personnelles.</li>
<li>Les corriger ou les supprimer.</li>
<li>Vous opposer à leur traitement.</li>
<li>Demander la portabilité de vos données.</li>
</ul>
<p>Pour exercer ces droits, contactez-nous à <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a>.</p>

<h2>9. Contact</h2>
<p>Pour toute question : <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a></p>',

            'terms_conditions' => '<h2>1. Acceptation des conditions</h2>
<p>En utilisant WhatsAppBizAI, vous acceptez ces conditions d\'utilisation. Si vous n\'acceptez pas, veuillez ne pas utiliser le service.</p>

<h2>2. Description du service</h2>
<p>WhatsAppBizAI est un outil de back-office intelligent pour PME, accessible via WhatsApp. Il comprend :</p>
<ul>
<li>Un agent IA qui répond à vos clients sur WhatsApp.</li>
<li>La génération automatique de devis et factures PDF.</li>
<li>Les relances automatiques de factures impayées.</li>
<li>Un CRM et un tableau de bord de suivi.</li>
<li>Des outils de marketing par messagerie.</li>
</ul>

<h2>3. Inscription et compte</h2>
<p>Vous devez fournir des informations exactes lors de l\'inscription. Vous êtes responsable de la sécurité de votre compte et de votre mot de passe.</p>

<h2>4. Utilisation acceptable</h2>
<p>Vous vous engagez à :</p>
<ul>
<li>Ne pas utiliser le service à des fins illégales ou frauduleuses.</li>
<li>Ne pas envoyer de spam ou de messages non sollicités.</li>
<li>Ne pas tenter de contourner les mesures de sécurité.</li>
<li>Respecter les lois locales et internationales sur la protection des données.</li>
</ul>

<h2>5. Tarifs et paiement</h2>
<p>Les tarifs sont disponibles sur notre page <a href="' . url('pricing') . '">Tarifs</a>. Les abonnements payants sont facturés mensuellement via Flutterwave. L\'annulation est possible à tout moment.</p>

<h2>6. Propriété intellectuelle</h2>
<p>Le code, le design et le contenu de WhatsAppBizAI sont protégés par le droit d\'auteur. Vous conservez la propriété de vos données clients et contenus que vous importez.</p>

<h2>7. Limitation de responsabilité</h2>
<p>WhatsAppBizAI est fourni "en l\'état". Nous ne garantissons pas l\'absence d\'interruption ou d\'erreur. Notre responsabilité est limitée au montant payé au cours des 12 derniers mois.</p>

<h2>8. Résiliation</h2>
<p>Vous pouvez supprimer votre compte à tout moment depuis votre dashboard. Nous pouvons suspendre votre compte en cas de violation de ces conditions.</p>

<h2>9. Modification des conditions</h2>
<p>Nous nous réservons le droit de modifier ces conditions. Les changements significatifs vous seront notifiés par email ou via l\'application.</p>

<h2>10. Contact</h2>
<p>Pour toute question : <a href="mailto:legal@whatsappbizai.com">legal@whatsappbizai.com</a></p>',

            'cookie_policy' => '<h2>1. Qu\'est-ce qu\'un cookie ?</h2>
<p>Un cookie est un petit fichier texte déposé sur votre appareil lors de votre visite sur un site web. Il permet au site de mémoriser vos actions et préférences.</p>

<h2>2. Cookies utilisés</h2>
<p>WhatsAppBizAI utilise uniquement des cookies strictement nécessaires au fonctionnement du site :</p>
<ul>
<li><strong>Session :</strong> pour maintenir votre connexion authentifiée.</li>
<li><strong>Préférences de langue :</strong> pour mémoriser votre choix (FR/EN).</li>
<li><strong>Préférences de devise :</strong> pour mémoriser votre devise affichée.</li>
<li><strong>Consentement cookies :</strong> pour mémoriser votre acceptation.</li>
</ul>

<h2>3. Cookies que nous n\'utilisons PAS</h2>
<ul>
<li>Aucun cookie publicitaire ou de tracking.</li>
<li>Aucun cookie de tiers (Google Analytics, Facebook Pixel, etc.).</li>
<li>Aucun cookie de profilage ou de ciblage publicitaire.</li>
</ul>

<h2>4. Gestion des cookies</h2>
<p>Vous pouvez gérer ou supprimer les cookies via les paramètres de votre navigateur. La désactivation des cookies de session peut affecter le fonctionnement du site.</p>

<h2>5. Contact</h2>
<p>Pour toute question sur les cookies : <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a></p>',

            'footer_description' => '<p>Agent IA WhatsApp pour PME africaines. Devis, factures, relances et CRM automatisés — le tout depuis votre WhatsApp existant.</p>',
        ]);

        $this->command->info('✅ Site settings seeded: legal + footer');
    }
}
