@props([
    'variant' => 'light', // light | dark | mark
    'size' => 'md', // sm | md | lg
    'subtitle' => null,
])

@php
    $boxSize = match ($size) {
        'sm' => 'w-8 h-8',
        'lg' => 'w-11 h-11',
        default => 'w-9 h-9',
    };
    $titleClass = $variant === 'dark' ? 'text-white' : 'text-slate-950';
    $subClass = $variant === 'dark' ? 'text-slate-400' : 'text-slate-500';
    $markBg = $variant === 'dark' ? '#DC2626' : '#B91C1C';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 select-none']) }}>
    <svg class="{{ $boxSize }} shrink-0" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <rect width="32" height="32" rx="8" fill="{{ $markBg }}"/>
        <path d="M10.5 11.25C10.5 9.73122 11.7312 8.5 13.25 8.5H21.5V12.25H14.25C13.8358 12.25 13.5 12.5858 13.5 13C13.5 13.4142 13.8358 13.75 14.25 13.75H18.75C20.8211 13.75 22.5 15.4289 22.5 17.5V19.75C22.5 21.8211 20.8211 23.5 18.75 23.5H10.5V19.75H17.75C18.1642 19.75 18.5 19.4142 18.5 19C18.5 18.5858 18.1642 18.25 17.75 18.25H13.25C11.7312 18.25 10.5 17.0188 10.5 15.5V11.25Z" fill="#FFFFFF"/>
        <circle cx="22.5" cy="9.5" r="2" fill="#FCA5A5"/>
    </svg>

    @if ($variant !== 'mark')
        <span class="leading-none">
            <span class="block font-extrabold tracking-tight text-base {{ $titleClass }}">SILIRA</span>
            @if ($subtitle)
                <span class="block text-[11px] font-medium mt-0.5 {{ $subClass }}">{{ $subtitle }}</span>
            @endif
        </span>
    @endif
</span>
