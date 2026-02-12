@php
    $percent = $getRecord()->total > 0
        ? round(($getRecord()->selesai / $getRecord()->total) * 100)
        : 0;

    $color = $percent >= 80 ? '#16a34a' : ($percent >= 60 ? '#f59e0b' : '#dc2626');
@endphp

<div class="flex items-center gap-2">
    <div class="relative w-10 h-10">
        <svg class="w-10 h-10 transform -rotate-90">
            <circle
                cx="20"
                cy="20"
                r="15"
                stroke="#e5e7eb"
                stroke-width="4"
                fill="transparent"
            />
            <circle
                cx="20"
                cy="20"
                r="15"
                stroke="{{ $color }}"
                stroke-width="4"
                fill="transparent"
                stroke-dasharray="{{ 2 * 3.14 * 15 }}"
                stroke-dashoffset="{{ 2 * 3.14 * 15 * (1 - $percent / 100) }}"
                stroke-linecap="round"
            />
        </svg>
        <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold">
            {{ $percent }}%
        </span>
    </div>
</div>
