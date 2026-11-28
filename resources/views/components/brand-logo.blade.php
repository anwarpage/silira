@props([
    'size' => 'md', // sm | md | lg
    'inverted' => false,
    'markOnly' => false,
    'withUin' => true,
    'subtitle' => null,
])

@php
    $markSize = match ($size) {
        'sm' => 'w-8 h-8',
        'lg' => 'w-10 h-10',
        default => 'w-9 h-9',
    };
    $dividerHeight = match ($size) {
        'sm' => 'h-5',
        'lg' => 'h-7',
        default => 'h-6',
    };
    $titleSize = match ($size) {
        'sm' => 'text-[17.5px]',
        'lg' => 'text-[21px]',
        default => 'text-[19px]',
    };
    $upperFill = $inverted ? '#93C5FD' : '#1E3A8A';
    $lowerFill = $inverted ? '#FFFFFF' : '#0F172A';
    $textColor = $inverted ? 'text-white' : 'text-[#0F172A]';
    $subColor = $inverted ? 'text-[#94A3B8]' : 'text-[#475569]';
    $dividerColor = $inverted ? 'bg-white/20' : 'bg-[#0F172A]/12';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    @if ($withUin)
        <span class="inline-flex items-center justify-center {{ $markSize }} shrink-0 {{ $inverted ? 'rounded-[7px] bg-white px-1 py-0.5' : '' }}">
            <img
                src="{{ asset('images/logo-uin-antasari.jpg') }}"
                alt="Logo UIN Antasari Banjarmasin"
                class="w-full h-full object-contain block"
            >
        </span>
        <span class="{{ $dividerHeight }} w-px {{ $dividerColor }} shrink-0" aria-hidden="true"></span>
    @endif

    {{-- Original dual-dialogue S monogram: Aspirasi Mahasiswa (atas) & Respon DPM (bawah) --}}
    <svg class="{{ $markSize }} shrink-0" viewBox="0 0 36 36" fill="none" aria-hidden="true">
        <path d="M4 15.5C4 8.6 9.6 4 16.8 4H28.5C30.6 4 32 5.4 32 7.5C32 11.4 28.8 14.5 24.8 14.5H16.5C12.6 14.5 9.6 16.4 7.8 19.3C6.8 20.9 4.5 20.3 4.2 18.4C4.05 17.4 4 16.4 4 15.5Z" fill="{{ $upperFill }}"/>
        <path d="M32 20.5C32 27.4 26.4 32 19.2 32H7.5C5.4 32 4 30.6 4 28.5C4 24.6 7.2 21.5 11.2 21.5H19.5C23.4 21.5 26.4 19.6 28.2 16.7C29.2 15.1 31.5 15.7 31.8 17.6C31.95 18.6 32 19.6 32 20.5Z" fill="{{ $lowerFill }}"/>
    </svg>

    @if (! $markOnly)
        <span class="leading-none">
            <span class="block font-semibold tracking-[-0.035em] {{ $titleSize }} {{ $textColor }}">Silira</span>
            @if ($subtitle)
                <span class="block text-[11.5px] font-normal mt-0.5 {{ $subColor }}">{{ $subtitle }}</span>
            @endif
        </span>
    @endif
</span>
