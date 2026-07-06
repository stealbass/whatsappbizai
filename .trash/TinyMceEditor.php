<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * Champ WYSIWYG TinyMCE auto-hébergé, compatible Livewire/Filament.
 *
 * Usage dans une ressource Filament :
 *   TinyMceEditor::make('description')->label('Description')->height(300)
 *
 * Le composant utilise Alpine.js + wire:ignore pour éviter les destructions
 * lors des re-renders Livewire. La valeur est synchronisée via $wire.entangle().
 */
class TinyMceEditor extends Field
{
    protected string $view = 'forms.components.tinymce-editor';

    protected int $height = 350;

    public function height(int $px): static
    {
        $this->height = $px;
        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
