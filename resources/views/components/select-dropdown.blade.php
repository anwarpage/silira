@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Pilih opsi...',
    'required' => false,
    'searchable' => false,
    'size' => 'md',
])

@php
    $inputId = $id ?? 'dropdown_' . $name . '_' . uniqid();
    $currentValue = old($name, $selected);
    $currentOption = collect($options)->first(fn ($opt) => (string) $opt['value'] === (string) $currentValue);
    $heightClass = $size === 'sm' ? 'h-[40px] px-3 text-[14px]' : 'h-[44px] px-3.5 text-[14.5px]';
@endphp

<div class="relative w-full" data-dropdown data-required="{{ $required ? '1' : '0' }}">
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $currentValue }}" data-dropdown-input>

    <button
        type="button"
        data-dropdown-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        class="w-full {{ $heightClass }} rounded-[9px] border border-[#0f172a]/18 bg-white text-left flex items-center justify-between gap-2.5 hover:border-[#0f172a]/35 focus:outline-none focus:border-[#1e3a8a] focus:ring-[3px] focus:ring-[#1e3a8a]/14 transition cursor-pointer"
    >
        <span class="flex items-center gap-2.5 min-w-0 truncate">
            @if (!empty($currentOption['dot']))
                <span class="w-2 h-2 rounded-full shrink-0 {{ $currentOption['dot'] }}" data-dropdown-dot></span>
            @else
                <span class="w-2 h-2 rounded-full shrink-0 hidden" data-dropdown-dot></span>
            @endif
            <span class="truncate {{ $currentOption ? 'text-[#0f172a] font-medium' : 'text-[#94a3b8]' }}" data-dropdown-label>
                {{ $currentOption['label'] ?? $placeholder }}
            </span>
        </span>

        <svg class="w-4 h-4 text-[#475569] shrink-0 transition-transform duration-150" data-dropdown-chevron fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div
        data-dropdown-panel
        role="listbox"
        class="hidden absolute left-0 right-0 z-50 mt-1.5 rounded-[10px] border border-[#0f172a]/12 bg-white shadow-xl shadow-[#0f172a]/8 overflow-hidden"
    >
        @if ($searchable)
            <div class="p-2 border-b border-[#0f172a]/8 bg-[#f8fafc]">
                <input
                    type="text"
                    data-dropdown-search
                    placeholder="Cari pilihan..."
                    class="w-full h-9 px-3 rounded-[7px] border border-[#0f172a]/15 bg-white text-[14px] text-[#0f172a] placeholder:text-[#94a3b8] focus:outline-none focus:border-[#1e3a8a]"
                >
            </div>
        @endif

        <div class="max-h-64 overflow-y-auto p-1.5 space-y-0.5" data-dropdown-list>
            @foreach ($options as $option)
                @php
                    $isSelected = (string) $option['value'] === (string) $currentValue;
                @endphp
                <button
                    type="button"
                    role="option"
                    aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                    data-dropdown-option
                    data-value="{{ $option['value'] }}"
                    data-label="{{ $option['label'] }}"
                    data-dot="{{ $option['dot'] ?? '' }}"
                    data-search-text="{{ strtolower($option['label'] . ' ' . ($option['description'] ?? '')) }}"
                    class="w-full text-left px-3 py-2 rounded-[7px] flex items-start justify-between gap-3 transition cursor-pointer {{ $isSelected ? 'bg-[#eff6ff] text-[#0f172a]' : 'text-[#0f172a] hover:bg-[#f8fafc]' }}"
                >
                    <span class="flex items-start gap-2.5 min-w-0">
                        @if (!empty($option['dot']))
                            <span class="w-2 h-2 rounded-full mt-2 shrink-0 {{ $option['dot'] }}"></span>
                        @endif
                        <span class="min-w-0">
                            <span class="block text-[14px] font-medium leading-snug">{{ $option['label'] }}</span>
                            @if (!empty($option['description']))
                                <span class="block text-[13px] text-[#475569] mt-0.5 leading-snug">{{ $option['description'] }}</span>
                            @endif
                        </span>
                    </span>

                    <svg class="w-4 h-4 text-[#1e3a8a] shrink-0 mt-1 {{ $isSelected ? '' : 'opacity-0' }}" data-option-check fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            @endforeach
        </div>
    </div>

    <p data-dropdown-error class="hidden mt-1.5 text-[13px] text-[#991b1b]"></p>
</div>
