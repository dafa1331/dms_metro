@php
    $record = $getRecord();

    $url = null;

    if ($record->status_dokumen === 'terima' && $record->file_name) {
        $url = asset('storage/' . $record->file_name);
    } elseif ($record->temp_path) {
        $url = route('preview.temp', ['document' => $record->opd_id]);
    }
@endphp

@if ($url)
    <iframe
        src="{{ $url }}#zoom=page-width"
        class="w-full border rounded-lg"
        style="height: 80vh;"
    ></iframe>
@else
    <div class="text-gray-500 text-center py-6">
        File tidak tersedia
    </div>
@endif
