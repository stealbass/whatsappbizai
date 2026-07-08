<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
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
    protected static ?string $slug = 'payments';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->is_super_admin ?? false;
    }

    public static function getModelLabel(): string
    {
        return 'Paiement';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Paiements';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Détails du paiement')
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
                            'manual'       => 'Manuel',
                            'flutterwave'  => 'Flutterwave',
                            'mtn_momo'     => 'MTN MoMo',
                            'orange_money' => 'Orange Money',
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
                        ->maxLength(255)
                        ->copyable(),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),
                ])->columns(4),

            Forms\Components\Section::make('Vérification')
                ->schema([
                    Forms\Components\DateTimePicker::make('verified_at')
                        ->label('Vérifié le')
                        ->nullable(),
                    Forms\Components\RichEditor::make('admin_notes')
                        ->label('Notes admin')
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                        ->columnSpanFull(),
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
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Vérifier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'      => 'verified',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Paiement vérifié')->success()->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'      => 'rejected',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Paiement rejeté')->danger()->send();
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
