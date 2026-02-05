@php
    $record = $getRecord();
    $url = null;

    if ($record->status_dokumen === 'terima' && $record->file_name) {
        $url = asset('storage/' . $record->file_name);
    }elseif($record->status_dokumen === 'perbaikan'){
        $url = route('preview.temp', ['file' => $record->temp_path]);
    }elseif ($record->temp_path) {
        // Gunakan path relatif dari storage/app
        $url = route('preview.temp', ['file' => $record->temp_path]);
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
