<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * TinyMce — Custom Filament field wrapping TinyMCE self-hosted.
 * Livewire-safe : uses wire:ignore + Alpine.js $wire.set() to sync state.
 *
 * Usage in a Resource form:
 *   TinyMce::make('description')->label('Description')->height(350)
 *   TinyMce::make('privacy_policy')->html()->height(500)
 */
class TinyMce extends Field
{
    protected string $view = 'filament.forms.components.tinymce';

    protected int    $height  = 350;
    protected bool   $isHtml  = true;   // always renders as rich HTML editor

    // ── Fluent config ─────────────────────────────────────────────────────

    public function height(int $px): static
    {
        $this->height = $px;
        return $this;
    }

    public function html(bool $condition = true): static
    {
        $this->isHtml = $condition;
        return $this;
    }

    // ── Getters for the Blade view ─────────────────────────────────────────

    public function getHeight(): int    { return $this->height; }
    public function isHtml(): bool      { return $this->isHtml; }
}
