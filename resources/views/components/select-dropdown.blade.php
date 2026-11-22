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
    $triggerPad = $size === 'sm' ? 'px-3 py-1.5 text-xs h-9' : 'px-3 py-2 text-sm min-h-[40px]';
@endphp

<div class="relative w-full" data-dropdown data-required="{{ $required ? '1' : '0' }}">
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $currentValue }}" data-dropdown-input>

    <button
        type="button"
        data-dropdown-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        class="w-full {{ $triggerPad }} rounded-lg border border-stone-300 bg-white text-left flex items-center justify-between gap-2 hover:border-stone-400 focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800 transition cursor-pointer"
    >
        <span class="flex items-center gap-2 min-w-0 truncate">
            @if (!empty($currentOption['dot']))
                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $currentOption['dot'] }}" data-dropdown-dot></span>
            @else
                <span class="w-1.5 h-1.5 rounded-full shrink-0 hidden" data-dropdown-dot></span>
            @endif
            <span class="truncate {{ $currentOption ? 'text-stone-900 font-medium' : 'text-stone-400' }}" data-dropdown-label>
                {{ $currentOption['label'] ?? $placeholder }}
            </span>
        </span>

        <svg class="w-3.5 h-3.5 text-stone-400 shrink-0 transition-transform duration-150" data-dropdown-chevron fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div
        data-dropdown-panel
        role="listbox"
        class="hidden absolute left-0 right-0 z-50 mt-1 rounded-lg border border-stone-200 bg-white shadow-lg shadow-stone-950/8 overflow-hidden"
    >
        @if ($searchable)
            <div class="p-1.5 border-b border-stone-100 bg-stone-50/80">
                <input
                    type="text"
                    data-dropdown-search
                    placeholder="Cari..."
                    class="w-full px-2.5 py-1.5 rounded-md border border-stone-200 bg-white text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:border-emerald-800"
                >
            </div>
        @endif

        <div class="max-h-60 overflow-y-auto p-1 space-y-0.5" data-dropdown-list>
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
                    class="w-full text-left px-2.5 py-2 rounded-md flex items-start justify-between gap-2.5 transition cursor-pointer {{ $isSelected ? 'bg-stone-100 text-stone-950' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-950' }}"
                >
                    <span class="flex items-start gap-2 min-w-0">
                        @if (!empty($option['dot']))
                            <span class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0 {{ $option['dot'] }}"></span>
                        @endif
                        <span class="min-w-0">
                            <span class="block text-xs font-medium leading-snug">{{ $option['label'] }}</span>
                            @if (!empty($option['description']))
                                <span class="block text-[11px] text-stone-500 mt-0.5 leading-snug">{{ $option['description'] }}</span>
                            @endif
                        </span>
                    </span>

                    <svg class="w-3.5 h-3.5 text-emerald-800 shrink-0 mt-0.5 {{ $isSelected ? '' : 'opacity-0' }}" data-option-check fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            @endforeach
        </div>
    </div>
</div>
