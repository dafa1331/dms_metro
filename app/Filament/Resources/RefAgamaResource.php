<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefAgamaResource\Pages;
use App\Filament\Resources\RefAgamaResource\RelationManagers;
use App\Models\RefAgama;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RefAgamaResource extends Resource
{
    protected static ?string $model = RefAgama::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_agama')
                    ->numeric()
                    ->required()
                    ->disabled(fn ($record) => $record !== null),

                Forms\Components\TextInput::make('nama_agama')
                    ->required()
                    ->maxLength(20),

                Forms\Components\Toggle::make('status_aktif')
                    ->default(true),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_agama')->sortable(),
                Tables\Columns\TextColumn::make('nama_agama')->searchable(),
                Tables\Columns\IconColumn::make('status_aktif')->boolean(),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])

            ->defaultSort('id_agama');

            
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
            'index' => Pages\ListRefAgamas::route('/'),
            'create' => Pages\CreateRefAgama::route('/create'),
            'edit' => Pages\EditRefAgama::route('/{record}/edit'),
        ];
    }
}
