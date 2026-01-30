<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    /* =========================
     * FORM CREATE / EDIT
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('opd_id')
                ->label('OPD')
                ->relationship('opd', 'nama_opd')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('nip')
                ->label('NIP')
                ->required()
                ->maxLength(30),

            Forms\Components\Select::make('type')
                ->label('Jenis Dokumen')
                ->options([
                    'DRH' => 'DRH',
                    'SK_CPNS' => 'SK CPNS',
                    'SK_PNS' => 'SK PNS',
                    'SPMT' => 'SPMT',
                    'PERTEK' => 'PERTEK',
                ])
                ->required(),

            Forms\Components\FileUpload::make('file_name')
                ->label('Upload Dokumen')
                ->disk('public')
                ->directory(fn ($get) => 'dokumen/' . $get('type'))
                ->acceptedFileTypes(['application/pdf'])
                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get) {
                    return $get('type') . '_' . $get('nip') . '.' . $file->getClientOriginalExtension();
                })
                ->afterStateUpdated(function ($state, callable $set) {
                    if (! $state instanceof TemporaryUploadedFile) {
                        return;
                    }

                    $set('original_name', $state->getClientOriginalName());
                    $set('mime', $state->getMimeType());
                    $set('size', $state->getSize());
                })
                ->required(),

            Forms\Components\Hidden::make('original_name'),
            Forms\Components\Hidden::make('mime'),
            Forms\Components\Hidden::make('size'),

        ]);
    }

    /* =========================
     * TABLE VIEW
     * ========================= */
    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('nip')
                ->label('NIP')
                ->searchable(),

            Tables\Columns\TextColumn::make('file_name')
                ->label('Nama File')
                ->formatStateUsing(fn ($state) => basename($state))
                ->wrap(),

            Tables\Columns\BadgeColumn::make('status_dokumen')
                ->label('Status')
                ->colors([
                    'success' => 'terima',
                    'warning' => 'proses',
                    'danger'  => 'tolak',
                ]),
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->label('Detail')
                ->modalHeading('Detail Dokumen')
                ->modalWidth('7xl')
                ->extraModalFooterActions([
                    Tables\Actions\Action::make('terima')
                        ->label('Terima')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Document $record) =>
                            $record->update([
                                'status_dokumen' => 'terima',
                                'catatan'        => 'Dokumen valid',
                                'tanggal_verif'  => now(),
                            ])
                        ),

                    Tables\Actions\Action::make('tolak')
                        ->label('Tolak')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('catatan')
                                ->label('Catatan Penolakan')
                                ->required(),
                        ])
                        ->action(fn (Document $record, array $data) =>
                            $record->update([
                                'status_dokumen' => 'tolak',
                                'catatan'        => $data['catatan'],
                                'tanggal_verif'  => now(),
                            ])
                        ),
                ]),

            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
}


    /* =========================
     * DETAIL (MODAL VIEW)
     * ========================= */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informasi Dokumen')
                ->schema([
                    Infolists\Components\TextEntry::make('nip')->label('NIP'),
                    Infolists\Components\TextEntry::make('type')->label('Jenis Dokumen'),
                    Infolists\Components\TextEntry::make('status_dokumen')->label('Status'),
                    Infolists\Components\TextEntry::make('uploaded_at')->label('Upload')->dateTime(),
                    Infolists\Components\TextEntry::make('tanggal_verif')->label('Verifikasi')->dateTime(),
                    Infolists\Components\TextEntry::make('catatan')->label('Catatan'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('Preview Dokumen')
                ->schema([
                    Infolists\Components\ViewEntry::make('file_name')
                        ->view('filament.infolists.pdf-preview'),
                ]),
        ]);
    }

   
    public static function getPages(): array
    {
         return [
            'index'  => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit'   => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}