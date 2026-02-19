<x-filament::page>

    {{-- FILTER FORM --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- TABEL --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">No</th>
                    <th class="border px-3 py-2">Nama Jabatan</th>

                    @foreach($pangkats as $pangkat)
                        <th class="border px-3 py-2">
                            {{ $pangkat->golongan }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $row)
                    <tr>
                        <td class="border px-3 py-2 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ $row['nama_jabatan'] }}
                        </td>

                        @foreach($pangkats as $pangkat)
                            <td class="border px-3 py-2 text-center">
                                {{ $row['counts'][$pangkat->golongan] ?? 0 }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-filament::page>
