<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * TinyMce — Custom Filament field wrapping TinyMCE self-hosted.
 * Livewire-safe : uses wire:ignore + Alpine.js $wire.set() to sync state.
 *
 * Usage in a Resource form:
 *   RichEditor::make('description')->label('Description')
 *   RichEditor::make('privacy_policy')->html()
 */
class TinyMce extends Field
{
    protected string $view = 'filament.forms.components.tinymce';

    protected int    $height      = 350;
    protected bool   $isHtml      = true;
    protected string $editorPlaceholder = '';

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

    /**
     * Sets placeholder text shown inside TinyMCE when the editor is empty.
     * Overrides Field::placeholder() to store in a separate property
     * (avoids conflicts with Filament's own placeholder handling).
     */
    public function placeholder(string $text): static
    {
        $this->editorPlaceholder = $text;
        return $this;
    }

    // ── Getters for the Blade view ─────────────────────────────────────────

    public function getHeight(): int             { return $this->height; }
    public function isHtml(): bool               { return $this->isHtml; }
    public function getEditorPlaceholder(): string { return $this->editorPlaceholder; }
}
