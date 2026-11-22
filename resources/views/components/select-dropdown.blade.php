@props([
    'name',
    'id' => null,
    'options' => [], // array of ['value' => '', 'label' => '', 'description' => null, 'dot' => null]
    'selected' => null,
    'placeholder' => 'Pilih opsi...',
    'required' => false,
    'searchable' => false,
    'size' => 'md', // sm | md
])

@php
    $inputId = $id ?? 'dropdown_' . $name . '_' . uniqid();
    $currentValue = old($name, $selected);
    $currentOption = collect($options)->first(fn ($opt) => (string) $opt['value'] === (string) $currentValue);
    $triggerPad = $size === 'sm' ? 'px-3 py-2 text-xs' : 'px-3.5 py-2.5 text-sm';
@endphp

<div class="relative w-full" data-dropdown data-required="{{ $required ? '1' : '0' }}">
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $currentValue }}" data-dropdown-input>

    <button
        type="button"
        data-dropdown-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        class="w-full {{ $triggerPad }} rounded-xl border border-slate-300/90 bg-white text-left flex items-center justify-between gap-2.5 shadow-2xs hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-red-600/25 focus:border-red-600 transition cursor-pointer"
    >
        <span class="flex items-center gap-2 min-w-0 truncate">
            @if (!empty($currentOption['dot']))
                <span class="w-2 h-2 rounded-full shrink-0 {{ $currentOption['dot'] }}" data-dropdown-dot></span>
            @else
                <span class="w-2 h-2 rounded-full shrink-0 hidden" data-dropdown-dot></span>
            @endif
            <span class="truncate {{ $currentOption ? 'text-slate-900 font-medium' : 'text-slate-400' }}" data-dropdown-label>
                {{ $currentOption['label'] ?? $placeholder }}
            </span>
        </span>

        <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-150" data-dropdown-chevron fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div
        data-dropdown-panel
        role="listbox"
        class="hidden absolute left-0 right-0 z-50 mt-1.5 rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5 overflow-hidden"
    >
        @if ($searchable)
            <div class="p-2 border-b border-slate-100 bg-slate-50/70">
                <input
                    type="text"
                    data-dropdown-search
                    placeholder="Cari pilihan..."
                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600"
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
                    class="w-full text-left px-3 py-2 rounded-lg flex items-start justify-between gap-3 transition cursor-pointer {{ $isSelected ? 'bg-red-50/80 text-red-900' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-950' }}"
                >
                    <span class="flex items-start gap-2.5 min-w-0">
                        @if (!empty($option['dot']))
                            <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $option['dot'] }}"></span>
                        @endif
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold leading-snug">{{ $option['label'] }}</span>
                            @if (!empty($option['description']))
                                <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $option['description'] }}</span>
                            @endif
                        </span>
                    </span>

                    <svg class="w-4 h-4 text-red-700 shrink-0 mt-0.5 {{ $isSelected ? '' : 'opacity-0' }}" data-option-check fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            @endforeach
        </div>
    </div>
</div>
