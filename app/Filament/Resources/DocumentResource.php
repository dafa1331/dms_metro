<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    /* =========================
     * FORM
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
                ->required(),

            Forms\Components\Select::make('type')
                ->label('Jenis Dokumen')
                ->required()
                ->reactive()
                ->options([
                    'DRH'     => 'DRH',
                    'SK_CPNS' => 'SK CPNS',
                    'SK_PNS'  => 'SK PNS',
                    'SPMT'    => 'SPMT',
                    'PERTEK'  => 'PERTEK',
                    'PANGKAT' => 'SK Pangkat',
                    'JABATAN' =>  'SK Jabatan',
                    'PENDIDIKAN' => 'Pendidikan',
                    'DIKLAT' => 'Diklat',
                ])
                ->afterStateUpdated(function ($state, callable $set) {
                    $riwayatTypes = ['PANGKAT','JABATAN', 'PENDIDIKAN', 'DIKLAT'];
                    $set('is_riwayat', in_array($state, $riwayatTypes) ? 1 : 0);
                }),

            Forms\Components\Hidden::make('is_riwayat')->default(0),

            Forms\Components\TextInput::make('keterangan_riwayat')
                        ->label('Keterangan Riwayat')
                        ->visible(fn ($get) => in_array($get('type'), ['PANGKAT', 'JABATAN', 'PENDIDIKAN', 'DIKLAT']))
                        ->required(fn ($get) => in_array($get('type'), ['PANGKAT', 'JABATAN', 'PENDIDIKAN', 'DIKLAT'])),

            Forms\Components\FileUpload::make('file_name')
                ->label('Upload Dokumen')
                ->disk('public')
                ->directory(fn ($get) => 'dokumen/' . $get('type'))
                ->acceptedFileTypes(['application/pdf'])
                ->getUploadedFileNameForStorageUsing(
                    function (TemporaryUploadedFile $file, $get) {

                        $type = $get('type');
                        $nip = $get('nip');
                        $tanggal = now()->format('Ymd_His');

                        if ($type === 'PANGKAT' && $get('keterangan_riwayat')) {
                            $keterangan_riwayat = str_replace(['/', ' '], ['-', ''], $get('keterangan_riwayat'));

                            return "{$type}_{$nip}_{$keterangan_riwayat}_{$tanggal}." .
                                $file->getClientOriginalExtension();
                        }

                        return "{$type}_{$nip}_{$tanggal}." .
                            $file->getClientOriginalExtension();
                    }
                )
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state instanceof TemporaryUploadedFile) {
                        $set('original_name', $state->getClientOriginalName());
                        $set('mime', $state->getMimeType());
                        $set('size', $state->getSize());
                        $set('uploaded_at', now());
                        $set('status_dokumen', 'proses');
                    }
                })
                ->required(),

            Forms\Components\Hidden::make('original_name'),
            Forms\Components\Hidden::make('mime'),
            Forms\Components\Hidden::make('size'),
            Forms\Components\Hidden::make('uploaded_at'),
            Forms\Components\Hidden::make('status_dokumen')->default('proses'),
        ]);
    }

    /* =========================
     * TABLE
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')->searchable(),

                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')
                    ->label('Nama Pegawai')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type'),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nama File')
                    ->formatStateUsing(fn ($state) => basename($state))
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('status_dokumen')
                    ->colors([
                        'success' => 'terima',
                        'warning' => 'proses',
                        'danger'  => 'tolak',
                        'primary' => 'perbaikan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalWidth('7xl')
                    ->extraModalFooterActions([

                        /* ================= TERIMA ================= */
                        Tables\Actions\Action::make('terima')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action(function (Document $record) {

                                if ($record->temp_path) {

                                    $finalPath = 'dokumen/' .
                                        $record->type . '/' .
                                        basename($record->temp_path);

                                    \Storage::disk('public')->put(
                                        $finalPath,
                                        \Storage::disk('local')->get($record->temp_path)
                                    );

                                    \Storage::disk('local')->delete($record->temp_path);

                                    $record->update([
                                        'file_name' => $finalPath,
                                        'temp_path' => null,
                                    ]);
                                }

                                $record->update([
                                    'status_dokumen' => 'terima',
                                    'tanggal_verif' => now(),
                                ]);
                            })
                            ->visible(fn ($record) =>
                                in_array($record->status_dokumen, ['proses', 'perbaikan'])
                            ),

                        /* ================= TOLAK ================= */
                        Tables\Actions\Action::make('tolak')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->form([
                                Forms\Components\Textarea::make('catatan')
                                    ->required(),
                            ])
                            ->action(function (array $data, Document $record) {

                                if ($record->temp_path) {
                                    \Storage::disk('local')->delete($record->temp_path);
                                }

                                $record->update([
                                    'temp_path' => null,
                                    'status_dokumen' => 'tolak',
                                    'catatan' => $data['catatan'],
                                ]);
                            })
                            ->visible(fn ($record) =>
                                in_array($record->status_dokumen, ['proses', 'perbaikan'])
                            ),
                    ]),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    /* =========================
     * DETAIL
     * ========================= */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Informasi Dokumen')
                ->schema([
                    Infolists\Components\TextEntry::make('nip'),
                    Infolists\Components\TextEntry::make('type'),
                    Infolists\Components\TextEntry::make('keterangan_riwayat'),
                    Infolists\Components\TextEntry::make('status_dokumen'),
                    Infolists\Components\TextEntry::make('uploaded_at')->dateTime(),
                    Infolists\Components\TextEntry::make('tanggal_verif')->dateTime(),
                    Infolists\Components\TextEntry::make('catatan'),
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
