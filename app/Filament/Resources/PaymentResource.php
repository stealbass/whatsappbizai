<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\FlutterwaveService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Paiements';
    protected static ?string $modelLabel      = 'Paiement';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Détails du paiement')->schema([
                Forms\Components\Select::make('business_id')->label('Entreprise')
                    ->relationship('business', 'name')->required(),
                Forms\Components\Select::make('plan')->label('Plan')
                    ->options(['starter' => 'Starter', 'business' => 'Business', 'pro' => 'Pro'])
                    ->required(),
                Forms\Components\Select::make('billing_cycle')->label('Cycle')
                    ->options(['monthly' => 'Mensuel', 'yearly' => 'Annuel'])->required(),
                Forms\Components\Select::make('method')->label('Méthode')
                    ->options([
                        'flutterwave'   => 'Flutterwave',
                        'mtn_momo'      => 'MTN MoMo',
                        'orange_money'  => 'Orange Money',
                        'wave'          => 'Wave',
                        'bank_transfer' => 'Virement',
                        'other'         => 'Autre',
                    ])->required(),
                Forms\Components\TextInput::make('amount')->label('Montant')->numeric()->required(),
                Forms\Components\TextInput::make('currency')->label('Devise')->default('XAF'),
                Forms\Components\TextInput::make('reference')->label('Référence transaction'),
                Forms\Components\TextInput::make('phone_number')->label('Numéro'),
            ])->columns(2),

            Forms\Components\Section::make('Vérification admin')->schema([
                Forms\Components\Select::make('status')->label('Statut')
                    ->options(['pending' => '⏳ En attente', 'verified' => '✅ Vérifié', 'rejected' => '❌ Rejeté'])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')->label('Notes admin')->rows(3),
                Forms\Components\FileUpload::make('screenshot_path')
                    ->label('Capture d\'écran')->image()->disk('public')->directory('payment-proofs'),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')->label('Entreprise')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('plan')->label('Plan')
                    ->colors(['gray' => 'starter', 'primary' => 'business', 'success' => 'pro']),
                Tables\Columns\TextColumn::make('amount')->label('Montant')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 0, ',', ' ') . ' ' . $record->currency),
                Tables\Columns\BadgeColumn::make('method')->label('Méthode')
                    ->formatStateUsing(fn($state) => match($state) {
                        'mtn_momo' => 'MTN MoMo', 'orange_money' => 'Orange Money',
                        'wave' => 'Wave', 'bank_transfer' => 'Virement',
                        'flutterwave' => 'Flutterwave', default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors(['warning' => 'pending', 'success' => 'verified', 'danger' => 'rejected']),
                Tables\Columns\TextColumn::make('reference')->label('Référence')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'En attente', 'verified' => 'Vérifié', 'rejected' => 'Rejeté']),
                Tables\Filters\SelectFilter::make('method')
                    ->options(['flutterwave' => 'Flutterwave', 'mtn_momo' => 'MTN MoMo', 'orange_money' => 'Orange Money']),
            ])
            ->actions([
                // Vérifier et activer l'abonnement manuellement
                Tables\Actions\Action::make('verify')
                    ->label('✅ Vérifier & Activer')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Activer l\'abonnement')
                    ->modalDescription(fn(Payment $r) => "Vérifier le paiement de {$r->business?->name} ({$r->amount_formatted}) et activer l'abonnement {$r->plan} ?")
                    ->visible(fn(Payment $r) => $r->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Note de vérification (optionnel)')->rows(2),
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

                        $record->business->update(['plan' => $record->plan]);
                        $record->update([
                            'status'          => 'verified',
                            'subscription_id' => $subscription->id,
                            'verified_by'     => auth()->id(),
                            'verified_at'     => now(),
                            'admin_notes'     => $data['admin_notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title("Abonnement {$record->plan} activé pour {$record->business?->name}")
                            ->body("Expire le : " . $end->format('d/m/Y'))
                            ->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('❌ Rejeter')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Payment $r) => $r->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Raison du rejet')->required()->rows(2),
                    ])
                    ->action(function (Payment $record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Paiement rejeté')->warning()->send();
                    }),

                Tables\Actions\Action::make('view_proof')
                    ->label('🖼 Preuve')
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
