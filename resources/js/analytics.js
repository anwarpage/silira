import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    Filler,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
} from 'chart.js';

// White background plugin so exported PNGs look crisp on documents & slides
const whiteBackgroundPlugin = {
    id: 'siliraWhiteBackground',
    beforeDraw(chart) {
        const { ctx } = chart;
        ctx.save();
        ctx.globalCompositeOperation = 'destination-over';
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, chart.width, chart.height);
        ctx.restore();
    },
};

Chart.register(
    LineController,
    BarController,
    DoughnutController,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Filler,
    Tooltip,
    whiteBackgroundPlugin
);

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
Chart.defaults.font.family = "'DM Sans', ui-sans-serif, system-ui, -apple-system, sans-serif";
Chart.defaults.color = '#475569';
Chart.defaults.animation = prefersReducedMotion ? false : { duration: 220 };

document.addEventListener('DOMContentLoaded', () => {
    const dataEl = document.getElementById('siliraAnalyticsData');
    if (!dataEl) return;

    let payload;
    try {
        payload = JSON.parse(dataEl.textContent || '{}');
    } catch {
        return;
    }

    const { summary, activeTab = 'summary', questionsUrl = '', individualUrl = '' } = payload;
    const charts = {};

    // Toast feedback helper
    const toastEl = document.getElementById('analyticsToast');
    let toastTimer = null;
    const showToast = (message) => {
        if (!toastEl) return;
        toastEl.textContent = message;
        toastEl.classList.remove('hidden', 'opacity-0', 'translate-y-2');
        toastEl.classList.add('opacity-100', 'translate-y-0');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toastEl.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toastEl.classList.add('hidden'), 180);
        }, 2600);
    };

    const escapeHtml = (str) => String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

    const loadingMarkup = (label) => `
        <div class="s-card p-10 text-center">
            <div class="inline-flex items-center gap-2.5 text-[14px] text-[#475569]">
                <svg class="w-4 h-4 animate-spin text-[#1e3a8a]" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-85" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path>
                </svg>
                <span>${escapeHtml(label)}</span>
            </div>
        </div>`;

    // Export dropdown toggle
    const exportMenuBtn = document.getElementById('exportMenuTrigger');
    const exportMenuPanel = document.getElementById('exportMenuPanel');
    if (exportMenuBtn && exportMenuPanel) {
        const setExportOpen = (open) => {
            exportMenuPanel.classList.toggle('hidden', !open);
            exportMenuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        exportMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            setExportOpen(exportMenuPanel.classList.contains('hidden'));
        });
        document.addEventListener('click', (e) => {
            if (!exportMenuPanel.contains(e.target) && !exportMenuBtn.contains(e.target)) {
                setExportOpen(false);
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') setExportOpen(false);
        });
    }

    // Custom date range visibility toggle
    const periodSelect = document.getElementById('filterPeriod');
    const customDateFields = document.getElementById('customDateFields');
    if (periodSelect && customDateFields) {
        const syncCustomDates = () => {
            const isCustom = periodSelect.value === 'custom';
            customDateFields.classList.toggle('hidden', !isCustom);
            customDateFields.classList.toggle('grid', isCustom);
        };
        periodSelect.addEventListener('change', syncCustomDates);
        syncCustomDates();
    }

    // Helper for clean tooltip formatting
    const percentTooltip = (total) => ({
        backgroundColor: '#0f172a',
        titleColor: '#ffffff',
        bodyColor: '#e2e8f0',
        padding: 10,
        cornerRadius: 8,
        displayColors: true,
        callbacks: {
            label(ctx) {
                const value = typeof ctx.parsed === 'object' ? (ctx.parsed.x ?? ctx.parsed.y ?? 0) : ctx.parsed;
                const pct = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                return ` ${value} respons (${pct}%)`;
            },
        },
    });

    const totalReports = summary?.stats?.total ?? 0;

    // 1. Timeline Chart (Line / Area)
    const timelineCanvas = document.getElementById('chartTimeline');
    if (timelineCanvas && summary?.timeline) {
        charts.timeline = new Chart(timelineCanvas, {
            type: 'line',
            data: {
                labels: summary.timeline.labels,
                datasets: [{
                    label: 'Laporan Masuk',
                    data: summary.timeline.counts,
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.09)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1e3a8a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5,
                    fill: true,
                    tension: 0.28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.y} laporan masuk`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10, font: { size: 11.5 } },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11.5 } },
                        grid: { color: 'rgba(15, 23, 42, 0.06)' },
                    },
                },
            },
        });
    }

    // 2. Weekday Pattern Chart (Vertical Bar)
    const weekdayCanvas = document.getElementById('chartWeekday');
    if (weekdayCanvas && summary?.weekday) {
        charts.weekday = new Chart(weekdayCanvas, {
            type: 'bar',
            data: {
                labels: summary.weekday.labels,
                datasets: [{
                    label: 'Respons',
                    data: summary.weekday.counts,
                    backgroundColor: '#0284c7',
                    hoverBackgroundColor: '#1e3a8a',
                    borderRadius: 6,
                    maxBarThickness: 28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { tooltip: percentTooltip(totalReports) },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 12 } } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11.5 } }, grid: { color: 'rgba(15, 23, 42, 0.06)' } },
                },
            },
        });
    }

    // 3. Jenis Laporan (Doughnut)
    const jenisCanvas = document.getElementById('chartJenis');
    if (jenisCanvas && summary?.jenis) {
        charts.jenis = new Chart(jenisCanvas, {
            type: 'doughnut',
            data: {
                labels: summary.jenis.map((i) => i.label),
                datasets: [{
                    data: summary.jenis.map((i) => i.count),
                    backgroundColor: summary.jenis.map((i) => i.color),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '58%',
                plugins: { tooltip: percentTooltip(totalReports) },
            },
        });
    }

    // 4. Tingkat Privasi (Doughnut)
    const privacyCanvas = document.getElementById('chartPrivacy');
    if (privacyCanvas && summary?.privacy) {
        charts.privacy = new Chart(privacyCanvas, {
            type: 'doughnut',
            data: {
                labels: summary.privacy.map((i) => i.label),
                datasets: [{
                    data: summary.privacy.map((i) => i.count),
                    backgroundColor: summary.privacy.map((i) => i.color),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '58%',
                plugins: { tooltip: percentTooltip(totalReports) },
            },
        });
    }

    // 5. Kategori Bidang (Horizontal Bar)
    const categoryCanvas = document.getElementById('chartCategory');
    if (categoryCanvas && summary?.categories) {
        charts.category = new Chart(categoryCanvas, {
            type: 'bar',
            data: {
                labels: summary.categories.map((i) => i.label),
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: summary.categories.map((i) => i.count),
                    backgroundColor: '#1e3a8a',
                    hoverBackgroundColor: '#172554',
                    borderRadius: 6,
                    maxBarThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { tooltip: percentTooltip(totalReports) },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, font: { size: 11.5 } }, grid: { color: 'rgba(15, 23, 42, 0.06)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 12 }, color: '#0f172a' } },
                },
            },
        });
    }

    // 6. Status Penanganan (Vertical Bar)
    const statusCanvas = document.getElementById('chartStatus');
    if (statusCanvas && summary?.status) {
        charts.status = new Chart(statusCanvas, {
            type: 'bar',
            data: {
                labels: summary.status.map((i) => i.label),
                datasets: [{
                    label: 'Jumlah',
                    data: summary.status.map((i) => i.count),
                    backgroundColor: summary.status.map((i) => i.color),
                    borderRadius: 6,
                    maxBarThickness: 36,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { tooltip: percentTooltip(totalReports) },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11.5 } } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11.5 } }, grid: { color: 'rgba(15, 23, 42, 0.06)' } },
                },
            },
        });
    }

    // 7. Jurusan / Instansi (Horizontal Bar)
    const jurusanCanvas = document.getElementById('chartJurusan');
    if (jurusanCanvas && summary?.jurusan && summary.jurusan.length > 0) {
        charts.jurusan = new Chart(jurusanCanvas, {
            type: 'bar',
            data: {
                labels: summary.jurusan.map((i) => (i.label.length > 34 ? `${i.label.slice(0, 32)}…` : i.label)),
                datasets: [{
                    label: 'Pelapor',
                    data: summary.jurusan.map((i) => i.count),
                    backgroundColor: '#0f766e',
                    hoverBackgroundColor: '#115e59',
                    borderRadius: 6,
                    maxBarThickness: 20,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { tooltip: percentTooltip(totalReports) },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, font: { size: 11.5 } }, grid: { color: 'rgba(15, 23, 42, 0.06)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11.5 }, color: '#0f172a' } },
                },
            },
        });
    }

    // Copy Chart & Download PNG actions
    document.querySelectorAll('[data-chart-download]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const key = btn.dataset.chartDownload;
            const chart = charts[key];
            if (!chart) return;
            const link = document.createElement('a');
            link.href = chart.toBase64Image('image/png', 1);
            link.download = `grafik-${key}-silira.png`;
            link.click();
            showToast('Grafik PNG berhasil diunduh.');
        });
    });

    document.querySelectorAll('[data-chart-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const key = btn.dataset.chartCopy;
            const chart = charts[key];
            if (!chart) return;

            try {
                const canvas = chart.canvas;
                const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
                if (blob && navigator.clipboard && window.ClipboardItem) {
                    await navigator.clipboard.write([new window.ClipboardItem({ 'image/png': blob })]);
                    showToast('Grafik disalin ke clipboard.');
                    return;
                }
            } catch {
                // Fallback to download if clipboard permission is unavailable
            }

            const link = document.createElement('a');
            link.href = chart.toBase64Image('image/png', 1);
            link.download = `grafik-${key}-silira.png`;
            link.click();
            showToast('Browser membatasi clipboard; grafik diunduh sebagai PNG.');
        });
    });

    // =========================================================
    // TAB 2: PER PERTANYAAN (Lazy-loaded & cached)
    // =========================================================
    const qSelect = document.getElementById('questionSelect');
    const qPrev = document.getElementById('questionPrev');
    const qNext = document.getElementById('questionNext');
    const qCounter = document.getElementById('questionCounter');
    const qContainer = document.getElementById('questionContent');
    let questionsCache = null;
    let questionsLoading = false;
    let qIndex = 0;

    const renderQuestion = (idx) => {
        if (!qContainer || !questionsCache || !questionsCache.length) return;
        qIndex = Math.max(0, Math.min(idx, questionsCache.length - 1));
        const q = questionsCache[qIndex];

        if (qSelect) qSelect.value = String(qIndex);
        if (qCounter) qCounter.textContent = `${qIndex + 1} dari ${questionsCache.length}`;
        if (qPrev) qPrev.disabled = qIndex === 0;
        if (qNext) qNext.disabled = qIndex === questionsCache.length - 1;

        if (!q.groups || q.groups.length === 0) {
            qContainer.innerHTML = `
                <div class="s-card p-10 text-center">
                    <p class="text-[15px] font-medium text-[#0f172a]">Belum ada respons untuk pertanyaan ini</p>
                    <p class="text-[13.5px] text-[#475569] mt-1">Ubah filter di atas atau tunggu laporan baru masuk.</p>
                </div>`;
            return;
        }

        const groupsHtml = q.groups.map((g, gIdx) => {
            const ticketsHtml = g.tickets.map((t) => `
                <a href="${escapeHtml(t.url)}" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-[9px] bg-[#f8fafc] hover:bg-[#eff6ff] border border-[#0f172a]/8 transition">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[12px] text-[#475569]">${escapeHtml(t.ticket_code)}</span>
                            <span class="px-2 py-0.5 rounded-[5px] text-[11px] font-medium ${escapeHtml(t.status_badge_class)}">${escapeHtml(t.status_label)}</span>
                        </div>
                        <p class="text-[13.5px] font-medium text-[#0f172a] truncate mt-0.5">${escapeHtml(t.judul_laporan)}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[12px] text-[#475569]">${escapeHtml(t.nama_pelapor)}</p>
                        <p class="font-mono text-[11.5px] text-[#64748b]">${escapeHtml(t.created_at)}</p>
                    </div>
                </a>
            `).join('');

            const moreNote = g.more_count > 0
                ? `<p class="text-[12.5px] text-[#475569] mt-2">+ ${g.more_count} respons lainnya dengan jawaban serupa.</p>`
                : '';

            return `
                <div class="s-card overflow-hidden">
                    <div class="p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[15.5px] font-semibold text-[#0f172a] break-words">${escapeHtml(g.answer)}</p>
                                <div class="mt-2 w-full sm:w-64 h-1.5 rounded-full bg-[#f1f5f9] overflow-hidden">
                                    <div class="h-full bg-[#1e3a8a] rounded-full" style="width: ${Math.min(100, g.percent)}%"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="font-mono text-[13.5px] font-semibold text-[#1e3a8a] bg-[#eff6ff] px-2.5 py-1 rounded-[7px]">
                                    ${g.count} respons (${g.percent}%)
                                </span>
                                <button type="button" data-toggle-group="qgroup-${gIdx}" aria-expanded="${gIdx === 0 ? 'true' : 'false'}" class="h-9 px-3 rounded-[8px] border border-[#0f172a]/12 text-[13px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">
                                    ${gIdx === 0 ? 'Tutup daftar' : 'Lihat tiket'}
                                </button>
                            </div>
                        </div>
                        <div id="qgroup-${gIdx}" class="${gIdx === 0 ? '' : 'hidden'} mt-4 pt-4 border-t border-[#e5e7eb] space-y-2">
                            ${ticketsHtml}
                            ${moreNote}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        qContainer.innerHTML = `
            <div class="s-card p-5 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-l-4 border-l-[#1e3a8a]">
                <div>
                    <span class="inline-block text-[11.5px] font-semibold uppercase tracking-[0.06em] text-[#1e3a8a] bg-[#eff6ff] px-2 py-0.5 rounded-[5px] mb-1.5">${escapeHtml(q.type)}</span>
                    <h3 class="text-[18px] font-semibold text-[#0f172a]">${escapeHtml(q.title)}</h3>
                </div>
                <div class="flex items-center gap-5 text-[13px] text-[#475569] shrink-0">
                    <div>Terjawab <strong class="font-mono text-[#0f172a]">${q.answered_count}/${q.total_responses}</strong></div>
                    <div>Variasi <strong class="font-mono text-[#0f172a]">${q.unique_answers}</strong></div>
                </div>
            </div>
            <div class="space-y-3">${groupsHtml}</div>
        `;

        qContainer.querySelectorAll('[data-toggle-group]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.toggleGroup);
                if (!target) return;
                const willOpen = target.classList.contains('hidden');
                target.classList.toggle('hidden', !willOpen);
                btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                btn.textContent = willOpen ? 'Tutup daftar' : 'Lihat tiket';
            });
        });
    };

    const loadQuestionsIfNeeded = async () => {
        if (questionsCache || questionsLoading || !questionsUrl || !qContainer) return;
        questionsLoading = true;
        qContainer.innerHTML = loadingMarkup('Memuat rincian per pertanyaan...');

        try {
            const res = await fetch(questionsUrl, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('HTTP error');
            const data = await res.json();
            questionsCache = data.questions || [];

            if (qSelect && questionsCache.length) {
                qSelect.innerHTML = questionsCache
                    .map((q, i) => `<option value="${i}">${i + 1}. ${escapeHtml(q.title)} (${escapeHtml(q.type)})</option>`)
                    .join('');
                qSelect.disabled = false;
            }
            renderQuestion(0);
        } catch {
            qContainer.innerHTML = '<div class="s-card p-8 text-center text-[14px] text-[#991b1b]">Gagal memuat data pertanyaan. Silakan muat ulang halaman.</div>';
        } finally {
            questionsLoading = false;
        }
    };

    qSelect?.addEventListener('change', () => renderQuestion(Number(qSelect.value)));
    qPrev?.addEventListener('click', () => renderQuestion(qIndex - 1));
    qNext?.addEventListener('click', () => renderQuestion(qIndex + 1));

    // =========================================================
    // TAB 3: INDIVIDUAL (Lazy-loaded, server-paginated 1 by 1)
    // =========================================================
    const iSelect = document.getElementById('individualSelect');
    const iPrev = document.getElementById('individualPrev');
    const iNext = document.getElementById('individualNext');
    const iJump = document.getElementById('individualJump');
    const iTotalLabel = document.getElementById('individualTotalLabel');
    const iActions = document.getElementById('individualActions');
    const iPdfLink = document.getElementById('individualPdfLink');
    const iOpenLink = document.getElementById('individualOpenLink');
    const iContainer = document.getElementById('individualContent');

    let iPage = 1;
    let iTotal = totalReports;
    let iIndexLoaded = false;
    let iAbortController = null;
    const individualPageCache = new Map();

    const renderIndividualItem = (item) => {
        if (!iContainer) return;
        if (!item) {
            iActions?.classList.add('hidden');
            iActions?.classList.remove('flex');
            if (iPrev) iPrev.disabled = true;
            if (iNext) iNext.disabled = true;
            iContainer.innerHTML = `
                <div class="s-card p-10 text-center">
                    <p class="text-[15px] font-medium text-[#0f172a]">Belum ada respons individual pada filter ini</p>
                    <p class="text-[13.5px] text-[#475569] mt-1">Reset filter untuk melihat seluruh laporan masuk.</p>
                </div>`;
            return;
        }

        iActions?.classList.remove('hidden');
        iActions?.classList.add('flex');
        if (iJump) {
            iJump.value = String(iPage);
            iJump.max = String(Math.max(1, iTotal));
        }
        if (iTotalLabel) iTotalLabel.textContent = String(iTotal);
        if (iSelect && !iSelect.classList.contains('hidden')) iSelect.value = String(iPage);
        if (iPrev) iPrev.disabled = iPage <= 1;
        if (iNext) iNext.disabled = iPage >= iTotal;
        if (iPdfLink) iPdfLink.href = item.pdf_url;
        if (iOpenLink) iOpenLink.href = item.show_url;

        const responsesHtml = item.responses.length
            ? item.responses.map((r) => `
                <div class="border-l-2 ${r.is_internal ? 'border-[#d97706] bg-[#fffbeb]/60' : 'border-[#1e3a8a] bg-[#f8fafc]'} pl-4 pr-4 py-3 rounded-r-[9px]">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-[12.5px]">
                        <div class="flex items-center gap-2">
                            <strong class="text-[#0f172a]">${escapeHtml(r.sender_name)}</strong>
                            ${r.is_internal ? '<span class="px-1.5 py-0.5 rounded bg-[#fef3c7] text-[#92400e] text-[11px] font-semibold">Internal</span>' : ''}
                            ${r.status_to ? `<span class="text-[#1e3a8a] font-medium">&rarr; ${escapeHtml(r.status_to)}</span>` : ''}
                        </div>
                        <span class="font-mono text-[12px] text-[#64748b]">${escapeHtml(r.created_at)}</span>
                    </div>
                    <p class="mt-1.5 text-[14px] text-[#334155] whitespace-pre-line">${escapeHtml(r.message)}</p>
                </div>
            `).join('')
            : '<p class="text-[13.5px] text-[#475569]">Belum ada tanggapan tindak lanjut.</p>';

        const attachmentHtml = item.attachment_url
            ? `<a href="${escapeHtml(item.attachment_url)}" target="_blank" rel="noopener noreferrer" class="s-link text-[14px]">${escapeHtml(item.attachment_name || 'Lihat lampiran')} &nearr;</a>`
            : '<span class="text-[#64748b]">Tidak ada lampiran</span>';

        iContainer.innerHTML = `
            <div class="s-card overflow-hidden">
                <div class="px-6 py-5 border-b border-[#e5e7eb] bg-[#f8fafc]/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-[13px] font-semibold text-[#1e3a8a] bg-[#eff6ff] px-2.5 py-0.5 rounded-[6px]">${escapeHtml(item.ticket_code)}</span>
                            <span class="px-2.5 py-0.5 rounded-[6px] text-[12.5px] font-medium ${escapeHtml(item.status_badge_class)}">${escapeHtml(item.status_label)}</span>
                            <span class="text-[12.5px] text-[#475569]">${escapeHtml(item.privacy_label)}</span>
                        </div>
                        <h3 class="mt-2 text-[19px] font-semibold text-[#0f172a]">${escapeHtml(item.judul_laporan)}</h3>
                    </div>
                    <div class="text-left sm:text-right shrink-0">
                        <p class="text-[12px] text-[#64748b]">Waktu Masuk</p>
                        <p class="font-mono text-[13px] font-medium text-[#0f172a]">${escapeHtml(item.created_at)} WIB</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 border-b border-[#e5e7eb] text-[14px]">
                    <div>
                        <p class="text-[12px] font-medium text-[#64748b]">Jenis Laporan</p>
                        <p class="mt-0.5 font-medium text-[#0f172a]">${escapeHtml(item.jenis_laporan)}</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-[#64748b]">Kategori Bidang</p>
                        <p class="mt-0.5 font-medium text-[#0f172a]">${escapeHtml(item.category)}</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-[#64748b]">Pelapor &amp; NIM/NIP</p>
                        <p class="mt-0.5 font-medium text-[#0f172a]">${escapeHtml(item.nama_pelapor)} <span class="font-mono text-[#475569]">(${escapeHtml(item.nim_nip)})</span></p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-[#64748b]">Jurusan / Instansi</p>
                        <p class="mt-0.5 text-[#0f172a]">${escapeHtml(item.jurusan_instansi)}</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-[#64748b]">No. WhatsApp</p>
                        <p class="mt-0.5 font-mono text-[#0f172a]">${escapeHtml(item.no_wa)}</p>
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-[#64748b]">Tanggal &amp; Lokasi Kejadian</p>
                        <p class="mt-0.5 text-[#0f172a]">${escapeHtml(item.tanggal_kejadian)} &middot; ${escapeHtml(item.lokasi_kejadian)}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-[12px] font-medium text-[#64748b]">Lampiran Bukti</p>
                        <div class="mt-0.5">${attachmentHtml}</div>
                    </div>
                </div>

                <div class="p-6 border-b border-[#e5e7eb]">
                    <p class="text-[12px] font-semibold uppercase tracking-[0.06em] text-[#64748b] mb-2">Isi Laporan / Aspirasi</p>
                    <p class="text-[14.5px] text-[#0f172a] leading-relaxed whitespace-pre-line">${escapeHtml(item.isi_laporan)}</p>
                </div>

                <div class="p-6">
                    <p class="text-[12px] font-semibold uppercase tracking-[0.06em] text-[#64748b] mb-3">Riwayat Tindak Lanjut (${item.responses_count})</p>
                    <div class="space-y-2.5">${responsesHtml}</div>
                </div>
            </div>
        `;
    };

    const loadIndividualPage = async (page) => {
        if (!individualUrl || !iContainer) return;
        const targetPage = Math.max(1, Math.min(page, Math.max(1, iTotal)));

        if (iIndexLoaded && individualPageCache.has(targetPage)) {
            iPage = targetPage;
            renderIndividualItem(individualPageCache.get(targetPage));
            return;
        }

        iAbortController?.abort();
        iAbortController = new AbortController();
        iContainer.innerHTML = loadingMarkup('Memuat detail respons...');

        try {
            const url = new URL(individualUrl, window.location.origin);
            url.searchParams.set('page', String(targetPage));
            if (!iIndexLoaded) url.searchParams.set('with_index', '1');

            const res = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
                signal: iAbortController.signal,
            });
            if (!res.ok) throw new Error('HTTP error');
            const data = await res.json();

            iTotal = data.total ?? 0;
            iPage = data.page ?? 1;

            if (!iIndexLoaded && Array.isArray(data.tickets)) {
                iIndexLoaded = true;
                if (iSelect && data.tickets.length > 0) {
                    iSelect.innerHTML = data.tickets
                        .map((t) => {
                            const title = t.judul_laporan.length > 36 ? `${t.judul_laporan.slice(0, 34)}…` : t.judul_laporan;
                            return `<option value="${t.page}">${escapeHtml(t.ticket_code)} — ${escapeHtml(title)}</option>`;
                        })
                        .join('');
                    iSelect.classList.remove('hidden');
                }
            }

            if (data.item) {
                individualPageCache.set(iPage, data.item);
            }
            renderIndividualItem(data.item ?? null);
        } catch (err) {
            if (err?.name === 'AbortError') return;
            iContainer.innerHTML = '<div class="s-card p-8 text-center text-[14px] text-[#991b1b]">Gagal memuat respons individual. Silakan coba lagi.</div>';
        }
    };

    iSelect?.addEventListener('change', () => loadIndividualPage(Number(iSelect.value)));
    iPrev?.addEventListener('click', () => loadIndividualPage(iPage - 1));
    iNext?.addEventListener('click', () => loadIndividualPage(iPage + 1));
    iJump?.addEventListener('change', () => {
        const num = parseInt(iJump.value, 10);
        if (!Number.isNaN(num)) loadIndividualPage(num);
    });

    // =========================================================
    // 3-Tab Switcher (Ringkasan / Per Pertanyaan / Individual)
    // =========================================================
    const tabButtons = Array.from(document.querySelectorAll('[data-analytics-tab]'));
    const tabPanels = Array.from(document.querySelectorAll('[data-analytics-panel]'));

    const activateTab = (tabName, updateUrl = true) => {
        tabButtons.forEach((btn) => {
            const active = btn.dataset.analyticsTab === tabName;
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
            btn.classList.toggle('text-[#1e3a8a]', active);
            btn.classList.toggle('border-[#1e3a8a]', active);
            btn.classList.toggle('font-semibold', active);
            btn.classList.toggle('text-[#475569]', !active);
            btn.classList.toggle('border-transparent', !active);
            btn.classList.toggle('font-medium', !active);
        });

        tabPanels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.analyticsPanel !== tabName);
        });

        if (updateUrl) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url.toString());
        }

        if (tabName === 'summary') {
            Object.values(charts).forEach((c) => c?.resize());
        } else if (tabName === 'question') {
            loadQuestionsIfNeeded();
        } else if (tabName === 'individual') {
            loadIndividualPage(iPage);
        }
    };

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => activateTab(btn.dataset.analyticsTab));
    });

    // Activate initial tab from server
    activateTab(activeTab, false);
});
