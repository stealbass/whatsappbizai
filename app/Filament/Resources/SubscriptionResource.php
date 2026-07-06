<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?int    $navigationSort  = 21;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.subscriptions');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.subscription');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.subscriptions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_financial');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('business_id')->label(__('app.admin.business'))
                ->relationship('business', 'name')->required(),
            Forms\Components\Select::make('plan')->label(__('app.admin.plan'))
                ->options(['free' => 'Gratuit', 'starter' => 'Starter', 'business' => 'Business', 'pro' => 'Pro'])
                ->required(),
            Forms\Components\Select::make('status')->label(__('app.admin.status'))
                ->options(['active' => __('app.admin.active'), 'expired' => __('app.admin.expired'), 'cancelled' => __('app.admin.cancelled'), 'pending' => __('app.admin.overdue')])
                ->required(),
            Forms\Components\Select::make('billing_cycle')->label(__('app.admin.billing_cycle'))
                ->options(['monthly' => __('app.admin.monthly'), 'yearly' => __('app.admin.yearly')])->required(),
            Forms\Components\DateTimePicker::make('starts_at')->label(__('app.admin.issue_date')),
            Forms\Components\DateTimePicker::make('ends_at')->label(__('app.admin.expiry')),
            Forms\Components\TextInput::make('amount_paid')->label(__('app.admin.amount'))->numeric(),
            Forms\Components\TextInput::make('currency')->label(__('app.admin.currency'))->default('XAF'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')->label(__('app.admin.business'))->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('plan')->label(__('app.admin.plan'))
                    ->colors(['gray' => 'free', 'warning' => 'starter', 'primary' => 'business', 'success' => 'pro']),
                Tables\Columns\BadgeColumn::make('status')->label(__('app.admin.status'))
                    ->colors(['success' => 'active', 'danger' => 'expired', 'gray' => 'cancelled', 'warning' => 'pending']),
                Tables\Columns\TextColumn::make('billing_cycle')->label(__('app.admin.billing_cycle'))
                    ->formatStateUsing(fn($s) => $s === 'yearly' ? __('app.admin.yearly') : __('app.admin.monthly')),
                Tables\Columns\TextColumn::make('ends_at')->label(__('app.admin.expiry'))
                    ->dateTime('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')->label(__('app.admin.amount'))
                    ->formatStateUsing(fn($s, $r) => number_format($s ?? 0, 0, ',', ' ') . ' ' . $r->currency),
                Tables\Columns\TextColumn::make('created_at')->label(__('app.admin.registered'))
                    ->dateTime('d/m/Y')->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->options(['free' => 'Gratuit', 'starter' => 'Starter', 'business' => 'Business', 'pro' => 'Pro']),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => __('app.admin.active'), 'expired' => __('app.admin.expired'), 'cancelled' => __('app.admin.cancelled')]),
            ])
            ->actions([
                Tables\Actions\Action::make('extend')
                    ->label(__('app.admin.extend_30_days'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn(Subscription $r) => $r->status === 'active')
                    ->action(function (Subscription $record) {
                        $record->update([
                            'ends_at' => ($record->ends_at ?? now())->addDays(30),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title(__('app.admin.subscription_extended'))->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit'   => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
