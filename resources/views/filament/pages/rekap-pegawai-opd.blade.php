<x-filament::page>

    <div class="space-y-2">
        @foreach($this->getOpdTree() as $opd)
            @include('filament.components.opd-tree', ['opd' => $opd])
        @endforeach
    </div>

</x-filament::page>