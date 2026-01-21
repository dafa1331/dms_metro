<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenjangJabatanResource\Pages;
use App\Models\JenjangJabatan;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class JenjangJabatanResource extends Resource
{
    protected static ?string $model = JenjangJabatan::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_jenjang')
                ->label('Nama Jenjang')
                ->required()
                ->maxLength(255),

                Forms\Components\Select::make('keterangan')
                ->required()
                ->options([
                    'keahlian' => 'Keahlian',
                    'keterampilan' => 'Keterampilan',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nama_jenjang')->searchable(),
                Tables\Columns\TextColumn::make('keahlian')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenjangJabatans::route('/'),
            'create' => Pages\CreateJenjangJabatan::route('/create'),
            'edit' => Pages\EditJenjangJabatan::route('/{record}/edit'),
        ];
    }
}
