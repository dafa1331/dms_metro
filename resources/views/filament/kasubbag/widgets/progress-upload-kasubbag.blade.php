<x-filament::card>
    <h2 class="text-lg font-semibold mb-4">Progress Upload</h2>

    @php
        $percent = $this->getData()['percent'];
    @endphp

    <div class="w-full bg-gray-200 rounded-full h-6">
        <div 
            class="bg-success-500 h-6 rounded-full text-center text-white text-sm font-semibold"
            style="width: {{ $percent }}%">
            {{ $percent }}%
        </div>
    </div>
</x-filament::card>
