<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\{
    TextInput,
    Select,
    Section
};

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // protected function afterSave(): void
    // {
    //     if (isset($this->data['roles'])) {
    //         $this->record->syncRoles($this->data['roles']);
    //     }
    // }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data User')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('password')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                    ]),

                Section::make('Role & OPD')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->required()
                            ->reactive(),

                        Select::make('opd_id')
                            ->label('OPD')
                            ->relationship('opd', 'nama_opd')
                            ->searchable()
                            ->required(fn (callable $get) =>
                                $get('roles') === ['kasubbag_kepegawaian']
                            )
                            ->hidden(fn (callable $get) =>
                                $get('roles') === ['admin']
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge(),

                Tables\Columns\TextColumn::make('opd.nama_opd')
                    ->label('OPD')
                    ->default('-'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
