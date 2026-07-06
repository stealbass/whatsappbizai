<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\TinyMce;
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
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.conversations');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.conversation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.conversations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_messaging');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('status')->label(__('app.admin.status'))
                    ->options(['open' => __('app.admin.open'), 'closed' => __('app.admin.closed'), 'waiting' => __('app.admin.waiting')])
                    ->required(),
                Forms\Components\Toggle::make('ai_enabled')->label(__('app.admin.ai')),
                TinyMce::make('summary')->label(__('app.admin.summary'))->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')->label(__('app.admin.contact'))
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact.whatsapp_number')->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')->label(__('app.admin.status'))
                    ->colors(['success' => 'open', 'gray' => 'closed', 'warning' => 'waiting']),
                Tables\Columns\IconColumn::make('ai_enabled')->label(__('app.admin.ai'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('messages_count')->label(__('app.admin.messages'))
                    ->counts('messages')->alignCenter(),
                Tables\Columns\TextColumn::make('last_message_at')->label(__('app.admin.last_message'))
                    ->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['open' => __('app.admin.open'), 'closed' => __('app.admin.closed'), 'waiting' => __('app.admin.waiting')]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('app.admin.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn(Conversation $r) => ConversationResource::getUrl('view', ['record' => $r])),
                Tables\Actions\Action::make('close')
                    ->label(__('app.admin.close'))
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
