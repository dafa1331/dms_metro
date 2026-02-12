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
use Filament\Tables\Filters\SelectFilter;

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

            Forms\Components\FileUpload::make('temp_path')
                ->disk('local')
                ->directory(fn ($get) => 'temp/dokumen/' . $get('type'))
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
                    }
                })
                ->required(),

            Forms\Components\Hidden::make('original_name'),
            Forms\Components\Hidden::make('mime'),
            Forms\Components\Hidden::make('size'),
            Forms\Components\Hidden::make('status_dokumen')->default('proses'),
        ]);
    }


    /* =========================
     * TABLE
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->filters([
                SelectFilter::make('status_dokumen')
                    ->label('Status Dokumen')
                    ->options([
                        'terima' => 'Terima',
                        'tolak'  => 'Tolak',
                        'proses' => 'Proses',
                        'perbaikan' => 'Perbaikan',
                    ]),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nip')->label('NIP'),
                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')->label('Nama Pegawai')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Jenis'),
                Tables\Columns\BadgeColumn::make('status_dokumen')->label('Status')->colors([
                    'warning' => 'proses',
                    'success' => 'terima',
                    'danger'  => 'tolak',
                    'primary' => 'perbaikan',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('perbaiki')
                    ->label('Perbaiki')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\FileUpload::make('temp_path')
                            ->required()
                            ->disk('local')
                            ->directory(fn ($get) => 'temp/dokumen/' . $get('type'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file, Document $record) {

                                    $keterangan_riwayat = $record->keterangan_riwayat
                                        ? str_replace(['/', ' '], ['-', ''], $record->keterangan_riwayat)
                                        : null;

                                    return $record->type
                                        . '_' . $record->nip
                                        . ($keterangan_riwayat ? '_' . $keterangan_riwayat : '')
                                        . '_' . now()->format('Ymd_His')
                                        . '.' . $file->getClientOriginalExtension();
                                }
                            ),
                        Forms\Components\Textarea::make('catatan'),
                    ])
                    ->action(function (array $data, Document $record) {

                        // ⬅️ INI STRING PATH, BUKAN OBJECT
                        $path = $data['temp_path'];

                        // ambil metadata dari storage
                        $fullPath = storage_path('app/' . $path);

                        $record->update([
                            'temp_path' => $path,
                            'status_dokumen' => 'perbaikan',
                            'catatan' => $data['catatan'] ?? null,
                            'original_name' => basename($path),
                            'mime' => mime_content_type($fullPath),
                            'size' => filesize($fullPath),
                            'uploaded_at' => now(),
                        ]);
                    })
                    ->visible(fn ($record) => $record->status_dokumen === 'tolak'),

            ])
            ->bulkActions([]);
    }

    /* =========================
     * INFOLIST / PREVIEW PDF
     * ========================= */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
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
            ])
            ->columns(1);
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

    /* =========================
     * PERMISSIONS
     * ========================= */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('kasubbag_kepegawaian') || $user->hasRole('bkpsdm');
    }

    /* =========================
     * HANDLE DOKUMEN YANG DITOLAK
     * ========================= */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $existing = Document::where('nip', $data['nip'])
            ->where('type', $data['type'])
            ->first();

        if ($existing && $existing->status_dokumen === 'tolak') {
            if ($existing->temp_path) {
                \Storage::disk('local')->delete($existing->temp_path);
            }

            $file = $data['temp_path'];

            if ($file instanceof TemporaryUploadedFile) {
                $path = $file->store('temp/dokumen/' . $data['type'], 'local');
                $originalName = $file->getClientOriginalName();
                $mime = $file->getMimeType();
                $size = $file->getSize();
            } else {
                $path = $file;
                $originalName = $data['original_name'] ?? null;
                $mime = $data['mime'] ?? null;
                $size = $data['size'] ?? null;
            }

            $existing->update([
                'temp_path' => $path,
                'original_name' => $originalName,
                'mime' => $mime,
                'size' => $size,
                'status_dokumen' => 'proses',
                'catatan' => null,
                'uploaded_at' => now(),
            ]);

            throw new \Filament\Support\Exceptions\Halt();
        }

        $data['status_dokumen'] = 'proses';
        $data['opd_id'] = auth()->user()->opd_id;
        $data['uploaded_at'] = now();

        return $data;
    }
}
