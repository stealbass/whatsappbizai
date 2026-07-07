<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Abonnements & Paiements';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Paiement';
    protected static ?string $modelLabelPlural = 'Paiements';
    protected static ?string $slug = 'payments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Détails du paiement')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\Select::make('business_id')
                        ->label('Entreprise')
                        ->relationship('business', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('method')
                        ->label('Méthode')
                        ->options([
                            'manual'        => 'Manuel',
                            'flutterwave'   => 'Flutterwave',
                            'mtn_momo'      => 'MTN MoMo',
                            'orange_money'  => 'Orange Money',
                        ])
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'pending'  => 'En attente',
                            'verified' => 'Vérifié',
                            'rejected' => 'Rejeté',
                        ])
                        ->required()
                        ->default('pending'),

                    Forms\Components\Select::make('plan')
                        ->label('Plan')
                        ->options([
                            'free'     => 'Free',
                            'starter'  => 'Starter',
                            'business' => 'Business',
                            'pro'      => 'Pro',
                        ])
                        ->required(),

                    Forms\Components\Select::make('billing_cycle')
                        ->label('Cycle')
                        ->options([
                            'monthly' => 'Mensuel',
                            'yearly'  => 'Annuel',
                        ])
                        ->required(),
                ])->columns(3),

            Forms\Components\Section::make('Montant')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('Montant')
                        ->numeric()
                        ->required()
                        ->suffix('XAF'),

                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XAF')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('reference')
                        ->label('Référence')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone_number')
                        ->label('Numéro de téléphone')
                        ->tel()
                        ->maxLength(50),
                ])->columns(4),

            Forms\Components\Section::make('Vérification')
                ->icon('heroicon-o-check-badge')
                ->schema([
                    Forms\Components\Select::make('verified_by')
                        ->label('Vérifié par')
                        ->relationship('verifier', 'name')
                        ->searchable()
                        ->nullable(),

                    Forms\Components\DateTimePicker::make('verified_at')
                        ->label('Date de vérification')
                        ->nullable(),

                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Notes admin')
                        ->rows(3),

                    Forms\Components\FileUpload::make('screenshot_path')
                        ->label('Capture d\'écran')
                        ->image()
                        ->directory('payments/screenshots')
                        ->nullable(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('business.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('Méthode')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'manual'       => 'gray',
                        'flutterwave'  => 'info',
                        'mtn_momo'     => 'warning',
                        'orange_money' => 'danger',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'  => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money($record?->currency ?? 'XAF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->label('Plan')
                    ->badge(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->limit(20)
                    ->copyable(),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Vérifié le')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'verified' => 'Vérifié',
                        'rejected' => 'Rejeté',
                    ]),

                Tables\Filters\SelectFilter::make('method')
                    ->label('Méthode')
                    ->options([
                        'manual'       => 'Manuel',
                        'flutterwave'  => 'Flutterwave',
                        'mtn_momo'     => 'MTN MoMo',
                        'orange_money' => 'Orange Money',
                    ]),

                Tables\Filters\SelectFilter::make('plan')
                    ->label('Plan')
                    ->options([
                        'free'     => 'Free',
                        'starter'  => 'Starter',
                        'business' => 'Business',
                        'pro'      => 'Pro',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Vérifier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Vérifier ce paiement ?')
                    ->action(function ($record) {
                        $record->update([
                            'status'     => 'verified',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Paiement vérifié')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rejeter ce paiement ?')
                    ->action(function ($record) {
                        $record->update([
                            'status'     => 'rejected',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Paiement rejeté')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
