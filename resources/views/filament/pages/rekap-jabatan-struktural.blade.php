<x-filament::page>

    {{-- FILTER FORM --}}
    <div class="mb-4">
        {{ $this->form }}
    </div>

    {{-- TABEL REKAP --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full text-sm border border-gray-300">
            
            {{-- HEADER --}}
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-center">No</th>
                    <th class="border px-3 py-2 text-left">Nama Jabatan</th>

                    @foreach($this->pangkats as $pangkat)
                        <th class="border px-3 py-2 text-center">
                            {{ $pangkat->golongan }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            {{-- BODY --}}
            <tbody>
                @forelse($this->data as $index => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-3 py-2 text-center">
                            {{ $index + 1 }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ $row['nama_jabatan'] }}
                        </td>

                        @foreach($this->pangkats as $pangkat)
                            <td class="border px-3 py-2 text-center">
                                {{ $row['counts'][$pangkat->golongan] ?? 0 }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + count($this->pangkats) }}"
                            class="border px-3 py-6 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</x-filament::page>
