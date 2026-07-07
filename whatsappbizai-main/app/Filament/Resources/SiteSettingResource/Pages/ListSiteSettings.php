<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    protected static ?string $title = 'Paramètres du site';

    public function mount(): void
    {
        redirect()->route('filament.admin.resources.site-settings.edit', [
            'record' => SiteSetting::instance()->id,
        ]);
    }
}
