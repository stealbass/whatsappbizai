<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected static ?string $title = 'Paramètres du site';

    public function getRecord(): SiteSetting
    {
        return SiteSetting::instance();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = SiteSetting::instance();

        return $record->toArray();
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $record->update($data);

        SiteSetting::refreshCache();

        Notification::make()
            ->title(__('app.admin.settings_saved'))
            ->success()
            ->send();

        return $record;
    }

    protected function getHeaderWidgets(): array
    {
        // HtmlEditorWidget removed — TinyMce custom field handles everything
        return [];
    }
}
