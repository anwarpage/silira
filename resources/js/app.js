document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = Array.from(document.querySelectorAll('[data-dropdown]'));

    const closeAll = (except = null) => {
        dropdowns.forEach((dd) => {
            if (dd === except) return;
            const panel = dd.querySelector('[data-dropdown-panel]');
            const trigger = dd.querySelector('[data-dropdown-trigger]');
            const chevron = dd.querySelector('[data-dropdown-chevron]');
            panel?.classList.add('hidden');
            trigger?.setAttribute('aria-expanded', 'false');
            chevron?.classList.remove('rotate-180');
        });
    };

    dropdowns.forEach((dd) => {
        const trigger = dd.querySelector('[data-dropdown-trigger]');
        const panel = dd.querySelector('[data-dropdown-panel]');
        const chevron = dd.querySelector('[data-dropdown-chevron]');
        const input = dd.querySelector('[data-dropdown-input]');
        const labelEl = dd.querySelector('[data-dropdown-label]');
        const dotEl = dd.querySelector('[data-dropdown-dot]');
        const searchInput = dd.querySelector('[data-dropdown-search]');
        const options = Array.from(dd.querySelectorAll('[data-dropdown-option]'));

        trigger?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = !panel.classList.contains('hidden');
            closeAll(dd);

            if (isOpen) {
                panel.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
                chevron?.classList.remove('rotate-180');
            } else {
                panel.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                chevron?.classList.add('rotate-180');
                if (searchInput) {
                    searchInput.value = '';
                    options.forEach((o) => o.classList.remove('hidden'));
                    setTimeout(() => searchInput.focus(), 20);
                }
            }
        });

        options.forEach((opt) => {
            opt.addEventListener('click', () => {
                const value = opt.dataset.value ?? '';
                const label = opt.dataset.label ?? '';
                const dot = opt.dataset.dot ?? '';

                input.value = value;
                labelEl.textContent = label;
                labelEl.classList.remove('text-slate-400');
                labelEl.classList.add('text-slate-900', 'font-medium');

                if (dotEl) {
                    dotEl.className = dot
                        ? `w-2 h-2 rounded-full shrink-0 ${dot}`
                        : 'w-2 h-2 rounded-full shrink-0 hidden';
                }

                options.forEach((o) => {
                    const active = o === opt;
                    o.setAttribute('aria-selected', active ? 'true' : 'false');
                    o.classList.toggle('bg-stone-100', active);
                    o.classList.toggle('text-stone-950', active);
                    o.classList.toggle('text-stone-700', !active);
                    const check = o.querySelector('[data-option-check]');
                    check?.classList.toggle('opacity-0', !active);
                });

                panel.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
                chevron?.classList.remove('rotate-180');
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        searchInput?.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            options.forEach((opt) => {
                const hay = opt.dataset.searchText || '';
                opt.classList.toggle('hidden', q !== '' && !hay.includes(q));
            });
        });
    });

    document.addEventListener('click', () => closeAll());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
    });

    // Enhanced file dropzones
    document.querySelectorAll('[data-dropzone]').forEach((zone) => {
        const fileInput = zone.querySelector('input[type="file"]');
        const fileLabel = zone.querySelector('[data-dropzone-filename]');
        if (!fileInput || !fileLabel) return;

        const updateName = () => {
            const file = fileInput.files?.[0];
            if (file) {
                const mb = (file.size / (1024 * 1024)).toFixed(2);
                fileLabel.textContent = `${file.name} (${mb} MB)`;
                fileLabel.classList.remove('text-slate-500');
                fileLabel.classList.add('text-slate-900', 'font-semibold');
            } else {
                fileLabel.textContent = 'Klik atau tarik file ke sini (JPG, PNG, WEBP, PDF maks. 5 MB)';
                fileLabel.classList.add('text-slate-500');
                fileLabel.classList.remove('text-slate-900', 'font-semibold');
            }
        };

        fileInput.addEventListener('change', updateName);
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('border-red-500', 'bg-red-50/40');
        });
        zone.addEventListener('dragleave', () => {
            zone.classList.remove('border-red-500', 'bg-red-50/40');
        });
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('border-red-500', 'bg-red-50/40');
            if (e.dataTransfer?.files?.length) {
                fileInput.files = e.dataTransfer.files;
                updateName();
            }
        });
    });
});
