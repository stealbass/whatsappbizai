# Modifications

## Problème : TinyMCE ne fonctionne pas dans Filament/Livewire

Le CDN TinyMCE (`cdn.tiny.cloud/1/no-api-key`) retourne une erreur 403, et l'éditeur ne s'affiche pas dans les pages admin Filament. Plusieurs approches ont été tentées.

---

### 1. Admin Retention — Passage de TinyMCE à Summernote

**Fichier :** `resources/views/filament/pages/retention.blade.php`

- Remplacement complet de TinyMCE par **Summernote** (CDN cdnjs)
- Ajout des dépendances CDN : jQuery 3.7.1 + Summernote 0.8.20 + locale FR
- Suppression du layout « stratégies » (remplacé par l'éditeur uniquement)
- Changement du script d'initialisation : plus de re-render pendant la frappe
- Sync du contenu uniquement au submit avec `$wire.set(..., false)` (pas de re-render)

**Fichier :** `app/Filament/Pages/RetentionCampaigns.php`

- Augmentation de `maxLength` de 1024 à 65535 pour accepter le HTML

---

### 2. TinyMCE global — Cache les notifications API key

**Fichier :** `resources/views/components/tinymce.blade.php`

- Ajout CSS `.tox-notification { display: none !important; }` — cache la bannière d'avertissement
- Ajout `promotion: false` dans la config TinyMCE
- Ajout `focusin` handler pour éviter le « grisé » de l'éditeur

---

### 3. SiteSetting HtmlEditor — Refonte de l'initialisation TinyMCE

**Fichier :** `resources/views/filament/widgets/html-editor.blade.php`

- Réécriture complète du script d'initialisation TinyMCE pour SiteSetting
- Ajout CSS `.tox-notification` pour cacher l'avertissement API key
- Ajout `promotion: false`
- Ajout handler `focusin`
- Support de `livewire:navigated` pour la navigation SPA
- Ajout du champ `footer_description` manquant
- Utilisation d'un container div au lieu d'un `display:none` sur la textarea

---

### 4. Auto-hébergement TinyMCE

**Nouveaux fichiers :** `public/vendor/tinymce/`

- Extraction de TinyMCE 6.8.6 depuis npm
- Copie des fichiers dans le répertoire public pour chargement local
- Plugins inclus : advlist, code, fullscreen, image, link, lists, preview, table
- Skin : oxide
- (Non utilisé actuellement — Summernote a été préféré)

---

### 5. AdminPanelProvider — Nettoyage

**Fichier :** `app/Providers/Filament/AdminPanelProvider.php`

- Suppression d'une ligne vide superflue
