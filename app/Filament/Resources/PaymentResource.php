<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\FlutterwaveService;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.payments');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.payments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_financial');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.admin.payment_details'))->schema([
                Forms\Components\Select::make('business_id')->label(__('app.admin.business'))
                    ->relationship('business', 'name')->required(),
                Forms\Components\Select::make('plan')->label(__('app.admin.plan'))
                    ->options(['starter' => 'Starter', 'business' => 'Business', 'pro' => 'Pro'])
                    ->required(),
                Forms\Components\Select::make('billing_cycle')->label(__('app.admin.billing_cycle'))
                    ->options(['monthly' => __('app.admin.monthly'), 'yearly' => __('app.admin.yearly')])->required(),
                Forms\Components\Select::make('method')->label(__('app.admin.method'))
                    ->options([
                        'flutterwave'   => 'Flutterwave',
                        'mtn_momo'      => 'MTN MoMo',
                        'orange_money'  => 'Orange Money',
                        'wave'          => 'Wave',
                        'bank_transfer' => __('app.admin.bank_transfer') ?? 'Virement',
                        'other'         => __('app.admin.other') ?? 'Autre',
                    ])->required(),
                Forms\Components\TextInput::make('amount')->label(__('app.admin.amount'))->numeric()->required(),
                Forms\Components\TextInput::make('currency')->label(__('app.admin.currency'))->default('XAF'),
                Forms\Components\TextInput::make('reference')->label(__('app.admin.reference')),
                Forms\Components\TextInput::make('phone_number')->label(__('app.admin.number')),
            ])->columns(2),

            Forms\Components\Section::make(__('app.admin.admin_verification'))->schema([
                Forms\Components\Select::make('status')->label(__('app.admin.status'))
                    ->options(['pending' => '⏳ ' . __('app.admin.pending'), 'verified' => '✅ ' . __('app.admin.verified'), 'rejected' => '❌ ' . __('app.admin.rejected')])
                    ->required(),
                RichEditor::make('admin_notes')->label(__('app.admin.admin_notes')),
                Forms\Components\FileUpload::make('screenshot_path')
                    ->label(__('app.admin.screenshot'))->image()->disk('public')->directory('payment-proofs'),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')->label(__('app.admin.business'))->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('plan')->label(__('app.admin.plan'))
                    ->colors(['gray' => 'starter', 'primary' => 'business', 'success' => 'pro']),
                Tables\Columns\TextColumn::make('amount')->label(__('app.admin.amount'))
                    ->formatStateUsing(fn($state, $record) => number_format($state, 0, ',', ' ') . ' ' . $record->currency),
                Tables\Columns\BadgeColumn::make('method')->label(__('app.admin.method'))
                    ->formatStateUsing(fn($state) => match($state) {
                        'mtn_momo' => 'MTN MoMo', 'orange_money' => 'Orange Money',
                        'wave' => 'Wave',                     'bank_transfer' => __('app.admin.bank_transfer') ?? 'Virement',
                        'flutterwave' => 'Flutterwave', default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')->label(__('app.admin.status'))
                    ->colors(['warning' => 'pending', 'success' => 'verified', 'danger' => 'rejected']),
                Tables\Columns\TextColumn::make('reference')->label(__('app.admin.reference'))->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('app.admin.issue_date'))->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => __('app.admin.pending'), 'verified' => __('app.admin.verified'), 'rejected' => __('app.admin.rejected')]),
                Tables\Filters\SelectFilter::make('method')
                    ->options(['flutterwave' => 'Flutterwave', 'mtn_momo' => 'MTN MoMo', 'orange_money' => 'Orange Money']),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label(__('app.admin.verify_activate'))
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('app.admin.activate_subscription'))
                    ->modalDescription(fn(Payment $r) => __('app.admin.activate_subscription') . " : {$r->business?->name} ({$r->amount_formatted}) - {$r->plan} ?")
                    ->visible(fn(Payment $r) => $r->status === 'pending')
                    ->form([
                        \App\Filament\Forms\Components\RichEditor::make('admin_notes')
                            ->label(__('app.admin.admin_notes')),
                    ])
                    ->action(function (Payment $record, array $data) {
                        $cycle = $record->billing_cycle;
                        $end   = $cycle === 'yearly' ? now()->addYear() : now()->addMonth();

                        $subscription = Subscription::create([
                            'business_id'  => $record->business_id,
                            'plan'         => $record->plan,
                            'status'       => 'active',
                            'billing_cycle'=> $cycle,
                            'starts_at'    => now(),
                            'ends_at'      => $end,
                            'amount_paid'  => $record->amount,
                            'currency'     => $record->currency,
                        ]);

                        $record->business->update([
                            'plan' => $record->plan,
                            'plan_expires_at' => $end,
                        ]);
                        $record->update([
                            'status'          => 'verified',
                            'subscription_id' => $subscription->id,
                            'verified_by'     => auth()->id(),
                            'verified_at'     => now(),
                            'admin_notes'     => $data['admin_notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title(__('app.admin.subscription_activated') . " {$record->business?->name}")
                            ->body(__('app.admin.expires_on') . " : " . $end->format('d/m/Y'))
                            ->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label(__('app.admin.reject'))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Payment $r) => $r->status === 'pending')
                    ->form([
                        \App\Filament\Forms\Components\RichEditor::make('admin_notes')
                            ->label(__('app.admin.rejection_reason'))->required(),
                    ])
                    ->action(function (Payment $record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title(__('app.admin.payment_rejected'))->warning()->send();
                    }),

                Tables\Actions\Action::make('view_proof')
                    ->label(__('app.admin.view_proof'))
                    ->color('gray')
                    ->visible(fn(Payment $r) => !empty($r->screenshot_path))
                    ->url(fn(Payment $r) => asset('storage/' . $r->screenshot_path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
