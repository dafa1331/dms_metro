<x-filament::page>

<div class="space-y-6">

    {{-- FILTER CARD --}}
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- OPD --}}
            <div>
                <label class="text-sm font-medium">OPD</label>
                <select wire:model.live="opd_id"
                    class="fi-input block w-full mt-1 rounded-lg border-gray-300 shadow-sm">
                    <option value="">Semua OPD</option>
                    @foreach($this->opdOptions as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- BULAN --}}
            <div>
                <label class="text-sm font-medium">
                    Bulan <span class="text-danger-600">*</span>
                </label>
                <select wire:model.live="bulan"
                    class="fi-input block w-full mt-1 rounded-lg border-gray-300 shadow-sm">
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}">
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TAHUN --}}
            <div>
                <label class="text-sm font-medium">
                    Tahun <span class="text-danger-600">*</span>
                </label>
                <select wire:model.live="tahun"
                    class="fi-input block w-full mt-1 rounded-lg border-gray-300 shadow-sm">
                    @foreach(range(date('Y')-5, date('Y')) as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- BUTTON AREA --}}
        <div class="flex justify-end gap-3 mt-6">

            <x-filament::button
                wire:click="loadData"
                color="primary"
                icon="heroicon-m-magnifying-glass">
                Tampilkan
            </x-filament::button>

            <x-filament::button
                wire:click="export"
                color="success"
                icon="heroicon-m-arrow-down-tray">
                Export Excel
            </x-filament::button>

        </div>
    </x-filament::section>


    {{-- TABLE --}}
    <x-filament::section>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">No</th>
                        <th class="px-3 py-2 border text-left">Nama Jabatan</th>

                        @foreach($jenjangList as $jenjang)
                            <th class="px-3 py-2 border text-center">
                                {{ $jenjang }}
                            </th>
                        @endforeach

                        <th class="px-3 py-2 border font-bold text-center">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $jabatan => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-3 py-2 border">
                                {{ $jabatan }}
                            </td>

                            @foreach($jenjangList as $jenjang)
                                <td class="px-3 py-2 border text-center">
                                    {{ $row[$jenjang] ?: '-' }}
                                </td>
                            @endforeach

                            <td class="px-3 py-2 border text-center font-semibold">
                                {{ $row['total'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center py-6 text-gray-500">
                                Data tidak tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

</div>

</x-filament::page>
