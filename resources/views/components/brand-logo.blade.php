@props([
    'size' => 'md', // sm | md | lg
    'inverted' => false,
    'markOnly' => false,
])

@php
    $boxSize = match ($size) {
        'sm' => 'w-7 h-7',
        'lg' => 'w-9 h-9',
        default => 'w-8 h-8',
    };
    $bg = $inverted ? '#E5E1D8' : '#2A1D17';
    $fg = $inverted ? '#2A1D17' : '#E5E1D8';
    $accent = $inverted ? '#456F3A' : '#7FB072';
    $textColor = $inverted ? 'text-[#e5e1d8]' : 'text-[#2a1d17]';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    <svg class="{{ $boxSize }} shrink-0" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <rect width="32" height="32" rx="8" fill="{{ $bg }}"/>
        <path d="M9.5 13.5C9.5 10.4624 11.9624 8 15 8H22.5V11.2H15C13.7297 11.2 12.7 12.2297 12.7 13.5C12.7 14.7703 13.7297 15.8 15 15.8H17C20.0376 15.8 22.5 18.2624 22.5 21.3V21.5C22.5 22.8807 21.3807 24 20 24H9.5V20.8H17.2C18.3598 20.8 19.3 19.8598 19.3 18.7C19.3 17.5402 18.3598 16.6 17.2 16.6H15C11.9624 16.6 9.5 14.1376 9.5 13.5Z" fill="{{ $fg }}"/>
        <rect x="9.5" y="16.2" width="3.2" height="3.2" rx="1.6" fill="{{ $accent }}"/>
        <rect x="19.3" y="12.6" width="3.2" height="3.2" rx="1.6" fill="{{ $accent }}"/>
    </svg>

    @if (! $markOnly)
        <span class="font-medium tracking-[-0.03em] text-[19px] leading-none {{ $textColor }}">Silira</span>
    @endif
</span>
