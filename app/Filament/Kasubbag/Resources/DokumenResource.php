<?php

namespace App\Filament\Kasubbag\Resources;

use App\Models\Document;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Kasubbag\Resources\DokumenResource\Pages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DokumenResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationLabel = 'Upload Dokumen';
    protected static ?string $pluralLabel = 'Upload Dokumen';
    protected static bool $shouldRegisterNavigation = true;
    // protected static ?string $navigationGroup = 'Dokumen';
    public static function canCreate(): bool
    {
        return Filament::auth()->user()?->hasRole('kasubbag_kepegawaian');
    }

    /* =========================
     * QUERY (OPD SENDIRI)
     * ========================= */
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        return parent::getEloquentQuery()
            ->when($user, fn ($q) =>
                $q->where('opd_id', $user->opd_id)
            );
    }

    /* =========================
     * FORM UPLOAD
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('opd_id')
                ->label('OPD')
                ->relationship('opd', 'nama_opd')
                ->default(fn () => Filament::auth()->user()?->opd_id)
                ->disabled()
                ->dehydrated()
                ->required(),

            Forms\Components\Select::make('nip')
                ->label('Pegawai')
                ->searchable()
                ->required()
                ->options(function () {
                    $user = Filament::auth()->user();
                    if (! $user) return [];

                    // ambil OPD induk + semua turunannya
                    $opdIds = \App\Models\Opd::where('id', $user->opd_id)
                        ->orWhere('parent_id', $user->opd_id)
                        ->pluck('id');

                    return Pegawai::whereHas('riwayatJabatan', function ($q) use ($opdIds) {
                            $q->whereIn('opd_id', $opdIds)
                            ->where('status_aktif', 1);
                        })
                        ->pluck('nama_lengkap', 'nip');
                })
                ->getOptionLabelUsing(fn ($value) =>
                    Pegawai::where('nip', $value)->value('nama_lengkap') . " ($value)"
            ),

            Forms\Components\Select::make('type')
                ->label('Jenis Dokumen')
                ->required()
                ->options([
                    'DRH'      => 'DRH',
                    'SK_CPNS'  => 'SK CPNS',
                    'SK_PNS'   => 'SK PNS',
                    'SPMT'     => 'SPMT',
                    'PERTEK'   => 'PERTEK',
                ]),

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

            Forms\Components\Hidden::make('status_dokumen')
                ->default('proses'),
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
                    ->label('NIP'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis'),

                Tables\Columns\BadgeColumn::make('status_dokumen')
                    ->label('Status')
                    ->colors([
                        'warning' => 'proses',
                        'success' => 'terima',
                        'danger'  => 'tolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

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

    /* =========================
     * PAGES
     * ========================= */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDokumens::route('/'),
            'create' => Pages\CreateDokumen::route('/create'),
        ];
    }
}
