@props([
    'size' => 'md', // sm | md | lg
    'inverted' => false,
    'markOnly' => false,
    'subtitle' => null,
])

@php
    $boxSize = match ($size) {
        'sm' => 'w-7 h-7',
        'lg' => 'w-9 h-9',
        default => 'w-8 h-8',
    };
    $bg = $inverted ? '#1E293B' : '#0F172A';
    $pillarSide = $inverted ? '#94A3B8' : '#CBD5E1';
    $pillarCenter = '#FFFFFF';
    $keystone = '#D99B26';
    $textColor = $inverted ? 'text-white' : 'text-[#0F172A]';
    $subColor = $inverted ? 'text-[#94A3B8]' : 'text-[#475569]';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    <svg class="{{ $boxSize }} shrink-0" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <rect width="32" height="32" rx="7" fill="{{ $bg }}"/>
        {{-- Architectural 3-pillar civic forum emblem: Mahasiswa · DPM · Fakultas --}}
        <path d="M7.5 14.5L11.5 11.5V23.5H7.5V14.5Z" fill="{{ $pillarSide }}"/>
        <path d="M14 9.5L18 12.2V23.5H14V9.5Z" fill="{{ $pillarCenter }}"/>
        <path d="M20.5 14.5L24.5 11.5V23.5H20.5V14.5Z" fill="{{ $pillarSide }}"/>
        <rect x="14" y="6.2" width="4" height="2.2" rx="1.1" fill="{{ $keystone }}"/>
    </svg>

    @if (! $markOnly)
        <span class="leading-none">
            <span class="block font-semibold tracking-[-0.03em] text-[18px] {{ $textColor }}">Silira</span>
            @if ($subtitle)
                <span class="block text-[12px] font-normal mt-0.5 {{ $subColor }}">{{ $subtitle }}</span>
            @endif
        </span>
    @endif
</span>
