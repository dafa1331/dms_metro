@php
    $level = $level ?? 1;

    $colors = [
        1 => 'bg-blue-100 text-blue-700',
        2 => 'bg-green-100 text-green-700',
        3 => 'bg-yellow-100 text-yellow-700',
        4 => 'bg-purple-100 text-purple-700',
    ];

    $badgeColor = $colors[$level] ?? 'bg-gray-100 text-gray-700';

    $pegawaiAktif = $opd->riwayatJabatan
        ->where('status_aktif', 1)
        ->unique('pegawai_id')
        ->count();
@endphp

<div 
    x-data="{ open: false }" 
    class="border rounded-xl bg-white shadow-sm"
>

    <!-- HEADER -->
    <div class="flex justify-between items-center p-3 hover:bg-gray-50">

        <div class="flex items-center gap-2">

            {{-- Tombol Expand --}}
            @if($opd->children->count())
                <button 
                    type="button"
                    @click.stop="open = !open"
                    class="flex items-center justify-center w-5 h-5 focus:outline-none"
                >
                    <svg 
                        class="w-4 h-4 transform transition-transform duration-200"
                        :class="{ 'rotate-90': open }"
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </button>
            @else
                <span class="w-5"></span>
            @endif

            {{-- Klik Nama → Detail --}}
            <a 
                href="{{ \App\Filament\Pages\DetailPegawaiOpd::getUrl(['record' => $opd->id]) }}"
                class="font-medium hover:underline"
            >
                {{ $opd->nama_opd }}
            </a>

        </div>

        {{-- Badge Jumlah Pegawai Aktif --}}
        <span class="text-xs px-2 py-1 rounded-lg {{ $badgeColor }}">
            {{ $pegawaiAktif }}
        </span>

    </div>

    {{-- CHILDREN --}}
    @if($opd->children->count())
        <div 
            x-show="open"
            x-collapse
            class="pl-6 pb-3 space-y-2"
        >
            @foreach($opd->children as $child)
                @include('filament.components.opd-tree', [
                    'opd' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif

</div>