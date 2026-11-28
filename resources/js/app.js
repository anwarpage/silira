document.addEventListener('DOMContentLoaded', () => {
    // Mobile navigation drawer toggles
    document.querySelectorAll('[data-mobile-toggle]').forEach((btn) => {
        const targetId = btn.dataset.mobileToggle;
        const panel = targetId ? document.getElementById(targetId) : null;
        if (!panel) return;

        const iconOpen = btn.querySelector('[data-icon-open]');
        const iconClose = btn.querySelector('[data-icon-close]');

        const setOpen = (open) => {
            panel.classList.toggle('hidden', !open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            iconOpen?.classList.toggle('hidden', open);
            iconClose?.classList.toggle('hidden', !open);
        };

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            setOpen(panel.classList.contains('hidden'));
        });

        document.addEventListener('click', (e) => {
            if (!panel.classList.contains('hidden') && !panel.contains(e.target) && !btn.contains(e.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !panel.classList.contains('hidden')) {
                setOpen(false);
            }
        });
    });

    // Slide-over mobile sidebar drawer (Admin)
    document.querySelectorAll('[data-drawer-open]').forEach((btn) => {
        const drawerId = btn.dataset.drawerOpen;
        const drawer = drawerId ? document.getElementById(drawerId) : null;
        const backdrop = document.getElementById('adminMobileBackdrop');
        if (!drawer) return;

        const setDrawer = (open) => {
            drawer.classList.toggle('hidden', !open);
            backdrop?.classList.toggle('hidden', !open);
            document.body.classList.toggle('overflow-hidden', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        btn.addEventListener('click', () => setDrawer(true));
        document.querySelectorAll(`[data-drawer-close="${drawerId}"]`).forEach((closer) => {
            closer.addEventListener('click', () => setDrawer(false));
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !drawer.classList.contains('hidden')) {
                setDrawer(false);
            }
        });
    });

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

        const visibleOptions = () => options.filter((o) => !o.classList.contains('hidden'));

        const openPanel = () => {
            closeAll(dd);
            panel.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            chevron?.classList.add('rotate-180');
            if (searchInput) {
                searchInput.value = '';
                options.forEach((o) => o.classList.remove('hidden'));
                setTimeout(() => searchInput.focus(), 20);
            }
        };

        const closePanel = () => {
            panel.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
            chevron?.classList.remove('rotate-180');
        };

        const selectOption = (opt) => {
            const value = opt.dataset.value ?? '';
            const label = opt.dataset.label ?? '';
            const dot = opt.dataset.dot ?? '';

            input.value = value;
            labelEl.textContent = label;
            labelEl.classList.remove('text-[#94a3b8]');
            labelEl.classList.add('text-[#0f172a]', 'font-medium');
            trigger.classList.remove('border-[#dc2626]', 'border-[#b91c1c]');
            const errEl = dd.querySelector('[data-dropdown-error]');
            if (errEl) {
                errEl.textContent = '';
                errEl.classList.add('hidden');
            }

            if (dotEl) {
                dotEl.className = dot
                    ? `w-2 h-2 rounded-full shrink-0 ${dot}`
                    : 'w-2 h-2 rounded-full shrink-0 hidden';
            }

            options.forEach((o) => {
                const active = o === opt;
                o.setAttribute('aria-selected', active ? 'true' : 'false');
                o.classList.toggle('bg-[#eff6ff]', active);
                const check = o.querySelector('[data-option-check]');
                check?.classList.toggle('opacity-0', !active);
            });

            closePanel();
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        trigger?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (panel.classList.contains('hidden')) {
                openPanel();
            } else {
                closePanel();
            }
        });

        trigger?.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (panel.classList.contains('hidden')) {
                    openPanel();
                }
                const list = visibleOptions();
                if (list.length) {
                    (e.key === 'ArrowDown' ? list[0] : list[list.length - 1]).focus();
                }
            }
        });

        panel?.addEventListener('keydown', (e) => {
            const list = visibleOptions();
            if (!list.length) return;
            const idx = list.indexOf(document.activeElement);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                list[(idx + 1 + list.length) % list.length].focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                list[(idx - 1 + list.length) % list.length].focus();
            } else if (e.key === 'Escape') {
                closePanel();
                trigger?.focus();
            }
        });

        options.forEach((opt) => {
            opt.addEventListener('click', () => selectOption(opt));
        });

        searchInput?.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            options.forEach((opt) => {
                const hay = opt.dataset.searchText || '';
                opt.classList.toggle('hidden', q !== '' && !hay.includes(q));
            });
        });

        // Client-side required enforcement for hidden inputs
        const form = dd.closest('form');
        if (form && dd.dataset.required === '1' && !form.dataset.dropdownValidationBound) {
            form.dataset.dropdownValidationBound = '1';
            form.addEventListener('submit', (e) => {
                const requiredDropdowns = Array.from(form.querySelectorAll('[data-dropdown][data-required="1"]'));
                const missing = requiredDropdowns.find((item) => {
                    const hidden = item.querySelector('[data-dropdown-input]');
                    return !hidden || hidden.value.trim() === '';
                });

                if (missing) {
                    e.preventDefault();
                    const missingTrigger = missing.querySelector('[data-dropdown-trigger]');
                    const errEl = missing.querySelector('[data-dropdown-error]');
                    missingTrigger?.classList.add('border-[#b91c1c]');
                    if (errEl) {
                        errEl.textContent = 'Mohon pilih salah satu opsi terlebih dahulu.';
                        errEl.classList.remove('hidden');
                    }
                    missingTrigger?.focus();
                    missingTrigger?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
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
        const defaultText = fileLabel.dataset.defaultText || fileLabel.textContent.trim();

        const updateName = () => {
            const file = fileInput.files?.[0];
            if (file) {
                const mb = (file.size / (1024 * 1024)).toFixed(2);
                fileLabel.textContent = `${file.name} (${mb} MB)`;
                fileLabel.classList.remove('text-[#475569]');
                fileLabel.classList.add('text-[#0f172a]', 'font-medium');
            } else {
                fileLabel.textContent = defaultText;
                fileLabel.classList.add('text-[#475569]');
                fileLabel.classList.remove('text-[#0f172a]', 'font-medium');
            }
        };

        fileInput.addEventListener('change', updateName);
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('border-[#1e3a8a]', 'bg-[#eff6ff]/70');
        });
        zone.addEventListener('dragleave', () => {
            zone.classList.remove('border-[#1e3a8a]', 'bg-[#eff6ff]/70');
        });
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('border-[#1e3a8a]', 'bg-[#eff6ff]/70');
            if (e.dataTransfer?.files?.length) {
                fileInput.files = e.dataTransfer.files;
                updateName();
            }
        });
    });

    // Prevent double-submit with subtle loading state
    document.querySelectorAll('form[method="POST"], form[method="post"]').forEach((form) => {
        if (form.dataset.noLoading === '1') return;
        form.addEventListener('submit', (e) => {
            if (e.defaultPrevented) return;
            if (form.dataset.confirmMessage && form.dataset.confirmed !== '1') return;
            const submitBtn = form.querySelector('button[type="submit"]:not([disabled])');
            if (!submitBtn) return;
            submitBtn.disabled = true;
            const loadingText = submitBtn.dataset.loadingText || 'Memproses...';
            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = `<svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-85" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path></svg><span>${loadingText}</span>`;
        });
    });

    // Custom confirmation modal for destructive actions
    const confirmModal = document.getElementById('sConfirmModal');
    if (confirmModal) {
        const msgEl = confirmModal.querySelector('[data-confirm-body]');
        const titleEl = confirmModal.querySelector('[data-confirm-title]');
        const acceptBtn = confirmModal.querySelector('[data-confirm-accept]');
        const cancelBtns = confirmModal.querySelectorAll('[data-confirm-cancel]');
        let pendingForm = null;

        const closeModal = () => {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
            pendingForm = null;
        };

        document.querySelectorAll('form[data-confirm-message]').forEach((form) => {
            form.addEventListener('submit', (e) => {
                if (form.dataset.confirmed === '1') return;
                e.preventDefault();
                pendingForm = form;
                if (titleEl) titleEl.textContent = form.dataset.confirmTitle || 'Konfirmasi penghapusan';
                if (msgEl) msgEl.textContent = form.dataset.confirmMessage;
                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');
                acceptBtn?.focus();
            });
        });

        acceptBtn?.addEventListener('click', () => {
            if (!pendingForm) return;
            const targetForm = pendingForm;
            targetForm.dataset.confirmed = '1';
            closeModal();
            targetForm.requestSubmit();
        });

        cancelBtns.forEach((btn) => btn.addEventListener('click', closeModal));
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) closeModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) closeModal();
        });
    }
});
