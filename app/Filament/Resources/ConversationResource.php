<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Conversations';
    protected static ?string $modelLabel = 'Conversation';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('status')->label('Statut')
                    ->options(['open' => 'Ouverte', 'closed' => 'Fermée', 'waiting' => 'En attente'])
                    ->required(),
                Forms\Components\Toggle::make('ai_enabled')->label('IA activée'),
                Forms\Components\Textarea::make('summary')->label('Résumé')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')->label('Contact')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact.whatsapp_number')->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors(['success' => 'open', 'gray' => 'closed', 'warning' => 'waiting']),
                Tables\Columns\IconColumn::make('ai_enabled')->label('IA')
                    ->boolean(),
                Tables\Columns\TextColumn::make('messages_count')->label('Messages')
                    ->counts('messages')->alignCenter(),
                Tables\Columns\TextColumn::make('last_message_at')->label('Dernier message')
                    ->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['open' => 'Ouverte', 'closed' => 'Fermée', 'waiting' => 'En attente']),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Conversation $r) => ConversationResource::getUrl('view', ['record' => $r])),
                Tables\Actions\Action::make('close')
                    ->label('Fermer')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn(Conversation $r) => $r->status === 'open')
                    ->action(fn(Conversation $r) => $r->update(['status' => 'closed', 'closed_at' => now()])),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConversations::route('/'),
            'view'   => Pages\ViewConversation::route('/{record}'),
            'edit'   => Pages\EditConversation::route('/{record}/edit'),
        ];
    }
}
