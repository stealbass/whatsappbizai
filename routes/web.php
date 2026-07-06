<?php

use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\QuoteController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\ConversationController;
use App\Http\Controllers\Client\SettingsController;
use App\Http\Controllers\Client\BroadcastController;
use App\Http\Controllers\Client\RetentionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — WhatsAppBizAI
|--------------------------------------------------------------------------
*/

// ─── INSTALLER (bloqué si déjà installé) ──────────────────────────────────
Route::middleware(\App\Http\Middleware\RedirectIfInstalled::class)->group(function () {
    Route::get('/install', [InstallController::class, 'show'])->name('install.show');
    Route::post('/install/check', [InstallController::class, 'checkRequirements'])->name('install.check');
    Route::post('/install/test-db', [InstallController::class, 'testConnection'])->name('install.test-db');
    Route::post('/install/run', [InstallController::class, 'setup'])->name('install.setup');
});

// ─── PUBLIC ───────────────────────────────────────────────────────────────
Route::get('/', fn() => view('landing'))->name('home');
Route::get('/sitemap.xml', fn() => response(view('sitemap'), 200, ['Content-Type' => 'application/xml']));
Route::get('/robots.txt', fn() => response(view('robots'), 200, ['Content-Type' => 'text/plain']));

// Pages légales & contact
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/contact', [PageController::class, 'contactForm'])->name('contact');
Route::post('/contact', [PageController::class, 'contactStore'])->name('contact.store');

// Auth client
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('google.callback');

// Dashboard client (protégé)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Contacts
    Route::get('/client/contacts', [ContactController::class, 'index'])->name('c.contacts');
    Route::get('/client/contacts/create', [ContactController::class, 'create'])->name('c.contacts.create');
    Route::post('/client/contacts', [ContactController::class, 'store'])->name('c.contacts.store');
    Route::get('/client/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('c.contacts.edit');
    Route::put('/client/contacts/{contact}', [ContactController::class, 'update'])->name('c.contacts.update');
    Route::delete('/client/contacts/{contact}', [ContactController::class, 'destroy'])->name('c.contacts.destroy');

    // Invoices
    Route::get('/client/invoices', [InvoiceController::class, 'index'])->name('c.invoices');
    Route::get('/client/invoices/create', [InvoiceController::class, 'create'])->name('c.invoices.create');
    Route::post('/client/invoices', [InvoiceController::class, 'store'])->name('c.invoices.store');
    Route::get('/client/invoices/{invoice}', [InvoiceController::class, 'show'])->name('c.invoices.show');
    Route::get('/client/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('c.invoices.edit');
    Route::put('/client/invoices/{invoice}', [InvoiceController::class, 'update'])->name('c.invoices.update');
    Route::post('/client/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('c.invoices.markPaid');
    Route::post('/client/invoices/{invoice}/reminder', [InvoiceController::class, 'sendReminder'])->name('c.invoices.reminder');
    Route::get('/client/invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('c.invoices.pdf');
    Route::post('/client/invoices/{invoice}/whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('c.invoices.whatsapp');
    Route::delete('/client/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('c.invoices.destroy');

    // Quotes
    Route::get('/client/quotes', [QuoteController::class, 'index'])->name('c.quotes');
    Route::get('/client/quotes/create', [QuoteController::class, 'create'])->name('c.quotes.create');
    Route::post('/client/quotes', [QuoteController::class, 'store'])->name('c.quotes.store');
    Route::get('/client/quotes/{quote}', [QuoteController::class, 'show'])->name('c.quotes.show');
    Route::get('/client/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('c.quotes.edit');
    Route::put('/client/quotes/{quote}', [QuoteController::class, 'update'])->name('c.quotes.update');
    Route::get('/client/quotes/{quote}/pdf', [QuoteController::class, 'generatePdf'])->name('c.quotes.pdf');
    Route::post('/client/quotes/{quote}/whatsapp', [QuoteController::class, 'sendWhatsApp'])->name('c.quotes.whatsapp');
    Route::post('/client/quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('c.quotes.convert');
    Route::delete('/client/quotes/{quote}', [QuoteController::class, 'destroy'])->name('c.quotes.destroy');

    // Services
    Route::get('/client/services', [ServiceController::class, 'index'])->name('c.services');
    Route::get('/client/services/create', [ServiceController::class, 'create'])->name('c.services.create');
    Route::post('/client/services', [ServiceController::class, 'store'])->name('c.services.store');
    Route::get('/client/services/{service}/edit', [ServiceController::class, 'edit'])->name('c.services.edit');
    Route::put('/client/services/{service}', [ServiceController::class, 'update'])->name('c.services.update');
    Route::delete('/client/services/{service}', [ServiceController::class, 'destroy'])->name('c.services.destroy');

    // Conversations
    Route::get('/client/conversations', [ConversationController::class, 'index'])->name('c.conversations');
    Route::get('/client/conversations/{conversation}', [ConversationController::class, 'show'])->name('c.conversations.show');

    // Settings
    Route::get('/client/settings/business', [SettingsController::class, 'business'])->name('c.settings.business');
    Route::put('/client/settings/business', [SettingsController::class, 'businessUpdate'])->name('c.settings.business.update');
    Route::get('/client/settings/whatsapp', [SettingsController::class, 'whatsapp'])->name('c.settings.whatsapp');
    Route::put('/client/settings/whatsapp', [SettingsController::class, 'whatsappUpdate'])->name('c.settings.whatsapp.update');
    Route::get('/client/settings/profile', [SettingsController::class, 'profile'])->name('c.settings.profile');
    Route::put('/client/settings/profile', [SettingsController::class, 'profileUpdate'])->name('c.settings.profile.update');
    Route::get('/client/settings/password', [SettingsController::class, 'password'])->name('c.settings.password');
    Route::put('/client/settings/password', [SettingsController::class, 'passwordUpdate'])->name('c.settings.password.update');
    Route::get('/client/settings/billing', [SettingsController::class, 'billing'])->name('c.settings.billing');

    // Broadcast
    Route::get('/client/broadcast', [BroadcastController::class, 'index'])->name('c.broadcast');
    Route::post('/client/broadcast', [BroadcastController::class, 'send'])->name('c.broadcast.send');

    // Retention (client relance SES contacts)
    Route::get('/client/retention', [RetentionController::class, 'index'])->name('c.retention');
    Route::post('/client/retention/send', [RetentionController::class, 'send'])->name('c.retention.send');
    Route::get('/client/retention/draft-ai', [RetentionController::class, 'draftAI'])->name('c.retention.draftAI');
    Route::post('/client/broadcast/draft-ai', [BroadcastController::class, 'draftAI'])->name('c.broadcast.draftAI');

    // Language switcher
    Route::get('/client/language/{locale}', [DashboardController::class, 'setLanguage'])->name('c.language');
});

// Inscription
Route::get('/register',  [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Tarification (public)
Route::get('/pricing', [PaymentController::class, 'pricing'])->name('payment.pricing');

// ─── ESPACE CLIENT (portail sans login, via token unique) ─────────────────
Route::prefix('client/{token}')->name('client.')->group(function () {
    Route::get('/',                                    [ClientPortalController::class, 'show'])->name('portal');
    Route::get('/invoice/{invoice}/download',          [ClientPortalController::class, 'downloadInvoice'])->name('invoice.download');
    Route::get('/quote/{quote}/download',              [ClientPortalController::class, 'downloadQuote'])->name('quote.download');
    Route::post('/quote/{quote}/accept',               [ClientPortalController::class, 'acceptQuote'])->name('quote.accept');
    Route::post('/quote/{quote}/decline',              [ClientPortalController::class, 'declineQuote'])->name('quote.decline');
});

// ─── AUTHENTIFIÉ ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Exports
    Route::get('/export/contacts', [ExportController::class, 'contacts'])->name('export.contacts');

    // Import contacts CSV
    Route::get('/import/contacts',  [ImportController::class, 'form'])->name('import.contacts.form');
    Route::post('/import/contacts', [ImportController::class, 'contacts'])->name('import.contacts');
    Route::get('/import/template',  [ImportController::class, 'template'])->name('import.template');

    // Paiement Flutterwave
    Route::post('/payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/payment/callback',  [PaymentController::class, 'callback'])->name('payment.callback');

    // Paiement manuel
    Route::get('/payment/manual',    [PaymentController::class, 'manualForm'])->name('payment.manual.form');
    Route::post('/payment/manual',   [PaymentController::class, 'manualStore'])->name('payment.manual.store');
});

// Admin language switcher (no auth required for the switch, but only admin accesses /admin)
Route::get('/admin/language/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en'])) {
        \Illuminate\Support\Facades\Session::put('locale', $locale);
        \Illuminate\Support\Facades\Cookie::queue('wbai_lang', $locale, 60 * 24 * 365);
        app()->setLocale($locale);
        config(['app.locale' => $locale]);
    }
    return redirect()->back();
});

// ─── WEBHOOK FLUTTERWAVE (sans CSRF) ─────────────────────────────────────
Route::post('/webhook/flutterwave', [PaymentController::class, 'webhook'])->name('webhook.flutterwave');
