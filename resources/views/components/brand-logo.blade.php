@props([
    'size' => 'md', // sm | md | lg
    'subtitle' => null,
    'markOnly' => false,
])

@php
    $boxSize = match ($size) {
        'sm' => 'w-7 h-7',
        'lg' => 'w-9 h-9',
        default => 'w-8 h-8',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    <svg class="{{ $boxSize }} shrink-0" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <rect width="32" height="32" rx="7" fill="#111110"/>
        <path d="M9 12.5L16 8.5L23 12.5V15.5L16 11.5L9 15.5V12.5Z" fill="#FFFFFF"/>
        <path d="M9 17.5L16 21.5L23 17.5V20.5L16 24.5L9 20.5V17.5Z" fill="#34D399"/>
    </svg>

    @if (! $markOnly)
        <span class="leading-none">
            <span class="block font-bold tracking-[-0.03em] text-[15px] text-stone-950">SILIRA</span>
            @if ($subtitle)
                <span class="block text-[11px] font-normal text-stone-500 mt-0.5">{{ $subtitle }}</span>
            @endif
        </span>
    @endif
</span>
