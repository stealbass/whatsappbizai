<?php

namespace App\Filament\Widgets;

use App\Models\Conversation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentConversationsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Conversations récentes';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Conversation::with(['contact'])
                    ->where('status', 'open')
                    ->latest('last_message_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->label(__('app.admin.contact'))
                    ->searchable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('contact.whatsapp_number')
                    ->label('WhatsApp')
                    ->color('gray'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('app.admin.status'))
                    ->colors(['success' => 'open', 'gray' => 'closed', 'warning' => 'waiting']),
                Tables\Columns\IconColumn::make('ai_enabled')
                    ->label(__('app.admin.ai'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('messages_count')
                    ->label(__('app.admin.messages'))
                    ->counts('messages')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label(__('app.admin.last_message'))
                    ->dateTime('d/m H:i')
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('app.admin.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn(Conversation $r) => route('filament.admin.resources.conversations.view', $r)),
            ])
            ->paginated(false);
    }
}
