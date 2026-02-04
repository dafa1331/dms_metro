<?php

namespace App\Filament\Kasubbag\Resources;

use App\Models\Document;
use App\Models\Pegawai;
use App\Models\Opd;
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
use Illuminate\Support\Facades\Auth;

class DokumenResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationLabel = 'Upload Dokumen';
    protected static ?string $pluralLabel = 'Upload Dokumen';

    public static function canCreate(): bool
    {
        return Filament::auth()->user()?->hasRole('kasubbag_kepegawaian');
    }

    /* =========================
     * QUERY → OPD DARI JABATAN AKTIF (+ CHILD)
     * ========================= */
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        $opdIds = Opd::find($user->opd_id)
            ?->getAllChildrenIds() ?? [];

        return parent::getEloquentQuery()
            ->whereHas('pegawai.jabatanAktif', function ($q) use ($opdIds) {
                $q->whereIn('opd_id', $opdIds);
            });
    }

    /* =========================
     * FORM
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('nip')
                ->label('Pegawai')
                ->searchable()
                ->required()
                ->options(function () {
                    $user = Filament::auth()->user();
                    if (! $user) return [];

                    $opdIds = Opd::find($user->opd_id)
                        ?->getAllChildrenIds() ?? [];

                    return Pegawai::whereHas('jabatanAktif', function ($q) use ($opdIds) {
                        $q->whereIn('opd_id', $opdIds);
                    })->pluck('nama_lengkap', 'nip');
                })
                ->getOptionLabelUsing(fn ($value) =>
                    Pegawai::where('nip', $value)->value('nama_lengkap') . " ($value)"
                ),

            Forms\Components\Select::make('type')
                ->label('Jenis Dokumen')
                ->required()
                ->options([
                    'DRH'     => 'DRH',
                    'SK_CPNS' => 'SK CPNS',
                    'SK_PNS'  => 'SK PNS',
                    'SPMT'    => 'SPMT',
                    'PERTEK'  => 'PERTEK',
                ]),

            Forms\Components\FileUpload::make('temp_path')
                ->label('Upload Dokumen')
                ->disk('local')
                ->directory(fn ($get) => 'temp/dokumen/' . $get('type'))
                ->acceptedFileTypes(['application/pdf'])
                ->getUploadedFileNameForStorageUsing(
                    fn (TemporaryUploadedFile $file, $get) =>
                        $get('type') . '_' . $get('nip') . '.' . $file->getClientOriginalExtension()
                )
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state instanceof TemporaryUploadedFile) {
                        $set('original_name', $state->getClientOriginalName());
                        $set('mime', $state->getMimeType());
                        $set('size', $state->getSize());
                    }
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
     * TABLE
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP'),

                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')
                    ->label('Nama Pegawai')
                    ->searchable(),

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
        return $infolist
        // ->maxWidth(MaxWidth::Full)
        ->schema([
            Infolists\Components\TextEntry::make('nip')->label('NIP'),
            Infolists\Components\TextEntry::make('type')->label('Jenis'),
            Infolists\Components\TextEntry::make('status_dokumen')->label('Status'),
            Infolists\Components\TextEntry::make('uploaded_at')->dateTime(),
            Infolists\Components\Section::make('Preview Dokumen')
                ->schema([
                    Infolists\Components\ViewEntry::make('file_name')
                        ->view('filament.infolists.pdf-preview')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
        return $infolist
        ->columns(1); // ⬅️ pentin
        
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDokumens::route('/'),
            'create' => Pages\CreateDokumen::route('/create'),
        ];
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('kasubbag_kepegawaian')
            || $user->hasRole('bkpsdm');
    }
    
}
