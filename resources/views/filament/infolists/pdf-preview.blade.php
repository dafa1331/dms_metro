@if ($getState())
    <iframe
        src="{{ asset('storage/' . $getState()) }}"
        class="w-full rounded-lg border"
        style="height: 600px;"
    ></iframe>
@else
    <div class="text-gray-500 text-center">
        File tidak tersedia
    </div>
@endif
