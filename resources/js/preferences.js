/**
 * WhatsAppBizAI — Language & Currency Preferences
 * Auto-detects browser language, persists in localStorage,
 * and dynamically updates text + prices on the page.
 *
 * NOTE: This file is the source copy. The production copy is at public/js/preferences.js
 */
(function () {
    'use strict';

    var XAF_RATES = {
        XAF: 1, NGN: 0.98, GHS: 0.08, KES: 0.11, ZAR: 0.028,
        UGX: 0.006, TZS: 0.006, RWF: 0.002, USD: 0.00165,
        EUR: 0.00152, GBP: 0.00128, CAD: 0.00222, CDF: 4.25,
        BIF: 4.65, GMD: 0.107, SLL: 40.0, LRD: 0.32, MWK: 2.85,
        SZL: 0.028, MGA: 0.0075, CVE: 0.15, XOF: 1, DJF: 0.28,
        KMF: 0.71, AED: 0.006, SAR: 0.006, EGP: 0.08, MAD: 0.015,
        TND: 0.005,
    };

    var SYMBOLS = {
        XAF: 'FCFA', NGN: '₦', GHS: 'GH₵', KES: 'KSh', ZAR: 'R',
        UGX: 'USh', TZS: 'TSh', RWF: 'FRw', USD: '$', EUR: '€',
        GBP: '£', CAD: 'C$', CDF: 'FC', BIF: 'FBu', GMD: 'D',
        SLL: 'Le', LRD: 'L$', MWK: 'MK', SZL: 'E', MGA: 'Ar',
        CVE: '$', XOF: 'CFA', DJF: 'Fdj', KMF: 'CF', AED: 'د.إ',
        SAR: '﷼', EGP: 'E£', MAD: 'MAD', TND: 'د.ت',
    };

    var LOCALES = {
        XAF: 'fr-CM', NGN: 'en-NG', GHS: 'en-GH', KES: 'en-KE',
        ZAR: 'en-ZA', UGX: 'en-UG', TZS: 'en-TZ', RWF: 'en-RW',
        USD: 'en-US', EUR: 'de-DE', GBP: 'en-GB', CAD: 'en-CA',
        CDF: 'fr-CD', BIF: 'fr-BI', GMD: 'en-GM', SLL: 'en-SL',
        LRD: 'en-LR', MWK: 'en-MW', SZL: 'en-SZ', MGA: 'fr-MG',
        CVE: 'pt-CV', XOF: 'fr-SN', DJF: 'fr-DJ', KMF: 'fr-KM',
        AED: 'ar-AE', SAR: 'ar-SA', EGP: 'ar-EG', MAD: 'fr-MA',
        TND: 'ar-TN',
    };

    var CURRENCY_COUNTRIES = {
        XAF: 'CM', NGN: 'NG', GHS: 'GH', KES: 'KE', ZAR: 'ZA',
        UGX: 'UG', TZS: 'TZ', RWF: 'RW', USD: 'US', EUR: 'FR',
        GBP: 'GB', CAD: 'CA', CDF: 'CD', BIF: 'BI', GMD: 'GM',
        SLL: 'SL', LRD: 'LR', MWK: 'MW', SZL: 'SZ', MGA: 'MG',
        CVE: 'CV', XOF: 'SN', DJF: 'DJ', KMF: 'KM', AED: 'AE',
        SAR: 'SA', EGP: 'EG', MAD: 'MA', TND: 'TN',
    };

    var AFTER_SYMBOL = ['XAF','NGN','KES','UGX','TZS','RWF','XOF','CDF','BIF','GNF','DJF','KMF','CVE'];

    function detectLanguage() {
        var s = localStorage.getItem('wbai_lang');
        if (s === 'fr' || s === 'en') return s;
        var nav = (navigator.language || navigator.userLanguage || '').toLowerCase();
        return nav.startsWith('fr') ? 'fr' : 'en';
    }

    function detectCurrency(lang) {
        var s = localStorage.getItem('wbai_currency');
        if (s && XAF_RATES[s]) return s;
        return lang === 'fr' ? 'XAF' : 'USD';
    }

    var currentLang = detectLanguage();
    var currentCurrency = detectCurrency(currentLang);
    var translations = window.__translations || {};

    function t(key) {
        var keys = key.split('.');
        var v = translations;
        for (var i = 0; i < keys.length; i++) {
            if (v && typeof v === 'object') v = v[keys[i]];
            else return key;
        }
        return v || key;
    }

    function convertFromXAF(amountXAF) {
        return Math.round(amountXAF * (XAF_RATES[currentCurrency] || 1));
    }

    function formatPrice(amount) {
        var loc = LOCALES[currentCurrency] || 'en-US';
        var sym = SYMBOLS[currentCurrency] || currentCurrency;
        try {
            var fmt = new Intl.NumberFormat(loc, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(amount);
            return AFTER_SYMBOL.indexOf(currentCurrency) !== -1
                ? fmt + ' ' + sym
                : sym + fmt;
        } catch (e) {
            return amount + ' ' + sym;
        }
    }

    function updateTranslations() {
        document.documentElement.lang = currentLang;

        document.querySelectorAll('[data-t-key]').forEach(function (el) {
            var val = t(el.getAttribute('data-t-key'));
            if (val) el.textContent = val;
        });

        document.querySelectorAll('[data-t-html]').forEach(function (el) {
            var val = t(el.getAttribute('data-t-html'));
            if (val) el.innerHTML = val;
        });

        document.querySelectorAll('[data-t-placeholder]').forEach(function (el) {
            var val = t(el.getAttribute('data-t-placeholder'));
            if (val) el.placeholder = val;
        });

        document.querySelectorAll('.lang-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-lang') === currentLang);
        });
    }

    function updatePrices() {
        document.querySelectorAll('[data-xaf]').forEach(function (el) {
            var xaf = parseInt(el.getAttribute('data-xaf'), 10);
            var period = el.getAttribute('data-period') || 'monthly';
            var converted = convertFromXAF(xaf);
            var periodLabel = period === 'yearly'
                ? (t('per_year') || '/yr')
                : (t('per_month') || '/mo');
            el.innerHTML = formatPrice(converted) + ' <span>' + periodLabel + '</span>';
        });

        document.querySelectorAll('input[name="currency"]').forEach(function (el) {
            el.value = currentCurrency;
        });

        document.querySelectorAll('.currency-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-currency') === currentCurrency);
        });
    }

    function switchLanguage(lang) {
        if (lang !== 'fr' && lang !== 'en') return;
        currentLang = lang;
        localStorage.setItem('wbai_lang', lang);
        document.cookie = 'wbai_lang=' + lang + ';path=/;max-age=31536000;SameSite=Lax';
        updateTranslations();
        updatePrices();
    }

    function switchCurrency(currency) {
        if (!XAF_RATES[currency]) return;
        currentCurrency = currency;
        localStorage.setItem('wbai_currency', currency);
        document.cookie = 'wbai_currency=' + currency + ';path=/;max-age=31536000;SameSite=Lax';
        updatePrices();
    }

    document.addEventListener('click', function (e) {
        var lb = e.target.closest('.lang-btn');
        if (lb) { e.preventDefault(); switchLanguage(lb.getAttribute('data-lang')); }

        var cb = e.target.closest('.currency-btn');
        if (cb) { e.preventDefault(); switchCurrency(cb.getAttribute('data-currency')); }
    });

    function init() {
        updateTranslations();
        updatePrices();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.WbaiPrefs = {
        setLanguage: switchLanguage,
        setCurrency: switchCurrency,
        getLanguage: function () { return currentLang; },
        getCurrency: function () { return currentCurrency; },
        convertFromXAF: convertFromXAF,
        formatPrice: formatPrice,
        CURRENCY_COUNTRIES: CURRENCY_COUNTRIES,
    };
})();
