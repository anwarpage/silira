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
    $heightClass = $size === 'sm' ? 'h-[42px] px-3 text-[14px]' : 'h-[48px] px-3.5 text-[15px]';
@endphp

<div class="relative w-full" data-dropdown data-required="{{ $required ? '1' : '0' }}">
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $currentValue }}" data-dropdown-input>

    <button
        type="button"
        data-dropdown-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        class="w-full {{ $heightClass }} rounded-[8px] border border-[#2a1d17]/24 bg-white text-left flex items-center justify-between gap-2.5 hover:border-[#2a1d17]/45 focus:outline-none focus:border-[#456f3a] focus:ring-[3px] focus:ring-[#456f3a]/16 transition cursor-pointer"
    >
        <span class="flex items-center gap-2.5 min-w-0 truncate">
            @if (!empty($currentOption['dot']))
                <span class="w-2 h-2 rounded-full shrink-0 {{ $currentOption['dot'] }}" data-dropdown-dot></span>
            @else
                <span class="w-2 h-2 rounded-full shrink-0 hidden" data-dropdown-dot></span>
            @endif
            <span class="truncate {{ $currentOption ? 'text-[#2a1d17] font-medium' : 'text-[#93867c]' }}" data-dropdown-label>
                {{ $currentOption['label'] ?? $placeholder }}
            </span>
        </span>

        <svg class="w-4 h-4 text-[#65564c] shrink-0 transition-transform duration-150" data-dropdown-chevron fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div
        data-dropdown-panel
        role="listbox"
        class="hidden absolute left-0 right-0 z-50 mt-1.5 rounded-[10px] border border-[#2a1d17]/18 bg-[#f6f4ef] shadow-xl shadow-[#2a1d17]/10 overflow-hidden"
    >
        @if ($searchable)
            <div class="p-2 border-b border-[#2a1d17]/10 bg-[#e5e1d8]/50">
                <input
                    type="text"
                    data-dropdown-search
                    placeholder="Cari pilihan..."
                    class="w-full h-9 px-3 rounded-[6px] border border-[#2a1d17]/20 bg-white text-[14px] text-[#2a1d17] placeholder:text-[#93867c] focus:outline-none focus:border-[#456f3a]"
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
                    class="w-full text-left px-3 py-2.5 rounded-[7px] flex items-start justify-between gap-3 transition cursor-pointer {{ $isSelected ? 'bg-[#e5e1d8] text-[#2a1d17]' : 'text-[#2a1d17] hover:bg-[#e5e1d8]/65' }}"
                >
                    <span class="flex items-start gap-2.5 min-w-0">
                        @if (!empty($option['dot']))
                            <span class="w-2 h-2 rounded-full mt-2 shrink-0 {{ $option['dot'] }}"></span>
                        @endif
                        <span class="min-w-0">
                            <span class="block text-[14px] font-medium leading-snug">{{ $option['label'] }}</span>
                            @if (!empty($option['description']))
                                <span class="block text-[13px] text-[#65564c] mt-0.5 leading-snug">{{ $option['description'] }}</span>
                            @endif
                        </span>
                    </span>

                    <svg class="w-4 h-4 text-[#456f3a] shrink-0 mt-1 {{ $isSelected ? '' : 'opacity-0' }}" data-option-check fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            @endforeach
        </div>
    </div>
</div>
