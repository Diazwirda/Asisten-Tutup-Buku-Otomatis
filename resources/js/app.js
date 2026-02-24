import Swal from 'sweetalert2';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

// ===== DOM READY =====
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDateDisplay();
    initFileUploads();
    initProcessButton();
    initRefreshResults();
    initResultTabs();
    initCharts();

    // ✅ Ambil data BI dari DB (summary + chart)
    loadBiReportFromDb();

    initCountAnimation();
    initSettingsForms();
    loadResultsData(); // Auto-load data on page load
});

// Global chart instance for dynamic updates
let pieChartInstance = null;

// ===== SIDEBAR TOGGLE =====
function initSidebar() {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
}

// ===== DATE DISPLAY =====
function initDateDisplay() {
    const dateEl = document.getElementById('header-date-text');
    if (!dateEl) return;

    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateEl.textContent = now.toLocaleDateString('id-ID', options);
}

// ===== FILE UPLOADS =====
function initFileUploads() {
    setupDropzone('gl-upload-zone', 'gl-file-input', 'gl-file-info', 'gl-file-name');
    setupDropzone('bs-upload-zone', 'bs-file-input', 'bs-file-info', 'bs-file-name');
}

function setupDropzone(zoneId, inputId, infoId, nameId) {
    const zone = document.getElementById(zoneId);
    const input = document.getElementById(inputId);
    const info = document.getElementById(infoId);
    const nameEl = document.getElementById(nameId);

    if (!zone || !input) return;

    // Click to browse
    zone.addEventListener('click', (e) => {
        if (e.target.closest('.upload-remove')) return;
        input.click();
    });

    // File selected
    input.addEventListener('change', () => {
        if (input.files.length > 0) {
            handleFileSelect(zone, input.files[0], info, nameEl);
        }
    });

    // Drag & Drop
    zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('drag-over');
    });

    zone.addEventListener('dragleave', () => {
        zone.classList.remove('drag-over');
    });

    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            if (['csv', 'xlsx', 'xls'].includes(ext)) {
                // Set file to input
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                handleFileSelect(zone, file, info, nameEl);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Didukung',
                    text: 'Hanya file .csv dan .xlsx yang diterima.',
                    background: '#1a1f35',
                    color: '#f1f5f9',
                    confirmButtonColor: '#6366f1',
                });
            }
        }
    });

    // Remove button
    const removeBtn = zone.querySelector('.upload-remove');
    if (removeBtn) {
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            input.value = '';
            zone.classList.remove('has-file');
            if (info) info.style.display = 'none';
            updateProcessButton();
        });
    }
}

function handleFileSelect(zone, file, infoEl, nameEl) {
    zone.classList.add('has-file');
    if (infoEl) infoEl.style.display = 'flex';
    if (nameEl) nameEl.textContent = file.name;
    updateProcessButton();

    Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    }).fire({
        icon: 'success',
        title: `File "${file.name}" berhasil dipilih`,
    });
}

// ===== PROCESS BUTTON =====
function updateProcessButton() {
    const btn = document.getElementById('btn-process');
    if (!btn) return;

    const glInput = document.getElementById('gl-file-input');
    const bsInput = document.getElementById('bs-file-input');

    const hasGL = glInput && glInput.files.length > 0;
    const hasBS = bsInput && bsInput.files.length > 0;

    btn.disabled = !(hasGL && hasBS);
}

function initProcessButton() {
    const btn = document.getElementById('btn-process');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        const glInput = document.getElementById('gl-file-input');
        const bsInput = document.getElementById('bs-file-input');

        if (!glInput.files[0] || !bsInput.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: 'File Belum Lengkap',
                text: 'Silakan upload kedua file terlebih dahulu.',
                background: '#1a1f35',
                color: '#f1f5f9',
                confirmButtonColor: '#6366f1',
            });
            return;
        }

        // Show processing popup
        Swal.fire({
            title: 'Memproses Data',
            html: `
                <div style="text-align:center;">
                    <div style="margin: 20px 0;">
                        <div style="width:60px;height:60px;border:3px solid rgba(99,102,241,0.15);border-top:3px solid #6366f1;border-radius:50%;animation:swal-spin 1s linear infinite;margin:0 auto;"></div>
                    </div>
                    <p style="color:#94a3b8;margin-bottom:8px;">Mengirim file ke server untuk diproses...</p>
                    <div style="height:4px;background:rgba(255,255,255,0.05);border-radius:4px;overflow:hidden;margin-top:16px;">
                        <div style="height:100%;width:30%;background:linear-gradient(90deg,#6366f1,#8b5cf6);border-radius:4px;animation:swal-progress 2s ease-in-out infinite;"></div>
                    </div>
                </div>
                <style>
                    @keyframes swal-spin { to { transform: rotate(360deg); } }
                    @keyframes swal-progress {
                        0% { width:0%;margin-left:0; }
                        50% { width:60%;margin-left:20%; }
                        100% { width:0%;margin-left:100%; }
                    }
                </style>
            `,
            background: '#1a1f35',
            color: '#f1f5f9',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });

        // Build FormData
        const formData = new FormData();
        formData.append('general_ledger', glInput.files[0]);
        formData.append('bank_statement', bsInput.files[0]);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            // Send to webhook
            const response = await fetch('/api/rekonsiliasi', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || `HTTP Error: ${result.status}`);
            }

            // Close processing popup
            Swal.close();

            // Show success popup
            Swal.fire({
                icon: 'success',
                title: 'File Berhasil Diproses!',
                html: '<p style="color:#94a3b8;">File sudah dikirim ke server. Klik tombol <strong style="color:#6366f1;">Refresh Data</strong> pada tabel di bawah untuk mengambil hasil rekonsiliasi.</p>',
                background: '#1a1f35',
                color: '#f1f5f9',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK',
            });

            // Show results section
            const resultsSection = document.getElementById('results-section');
            if (resultsSection) {
                resultsSection.style.display = 'block';
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Proses Gagal',
                html: `<p style="color:#94a3b8;">Terjadi kesalahan saat memproses data.</p>
                       <p style="color:#ef4444;font-size:0.85rem;margin-top:8px;">${error.message}</p>`,
                background: '#1a1f35',
                color: '#f1f5f9',
                confirmButtonColor: '#6366f1',
                confirmButtonText: 'Coba Lagi',
            });
        }
    });
}

// ===== REFRESH RESULTS =====
function initRefreshResults() {
    const refreshBtn = document.getElementById('btn-refresh-results');
    if (!refreshBtn) return;

    refreshBtn.addEventListener('click', async () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Show loading state on button
        const originalText = refreshBtn.innerHTML;
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="display:inline;vertical-align:middle;animation:swal-spin 1s linear infinite;">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            Memuat...
        `;

        try {
            const response = await fetch('/api/rekonsiliasi/results', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const data = await response.json();

            if (data.success) {
                populateResultTables(data.data, data.summary);
                updatePieChart(data.summary);
                const totalCount = data.summary.matched_count + data.summary.unmatched_count + data.summary.pair_not_found_count;
                if (totalCount > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Diperbarui!',
                        html: `
                            <div style="display:flex;gap:16px;justify-content:center;margin:16px 0;">
                                <div style="text-align:center;padding:12px 16px;background:rgba(16,185,129,0.1);border-radius:8px;border:1px solid rgba(16,185,129,0.2);">
                                    <div style="font-size:1.5rem;font-weight:700;color:#10b981;">${data.summary.matched_count}</div>
                                    <div style="font-size:0.75rem;color:#94a3b8;">Matched</div>
                                </div>
                                <div style="text-align:center;padding:12px 16px;background:rgba(239,68,68,0.1);border-radius:8px;border:1px solid rgba(239,68,68,0.2);">
                                    <div style="font-size:1.5rem;font-weight:700;color:#ef4444;">${data.summary.unmatched_count}</div>
                                    <div style="font-size:0.75rem;color:#94a3b8;">Unmatched</div>
                                </div>
                                <div style="text-align:center;padding:12px 16px;background:rgba(245,158,11,0.1);border-radius:8px;border:1px solid rgba(245,158,11,0.2);">
                                    <div style="font-size:1.5rem;font-weight:700;color:#f59e0b;">${data.summary.pair_not_found_count}</div>
                                    <div style="font-size:0.75rem;color:#94a3b8;">Not Found</div>
                                </div>
                            </div>
                        `,
                        background: '#1a1f35',
                        color: '#f1f5f9',
                        confirmButtonColor: '#10b981',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Belum Ada Data',
                        text: 'Hasil rekonsiliasi belum tersedia. Coba refresh lagi nanti.',
                        background: '#1a1f35',
                        color: '#f1f5f9',
                        confirmButtonColor: '#6366f1',
                    });
                }
            } else {
                throw new Error(data.message || 'Gagal mengambil data');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengambil Data',
                text: error.message,
                background: '#1a1f35',
                color: '#f1f5f9',
                confirmButtonColor: '#6366f1',
            });
        } finally {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = originalText;
        }
    });
}

// ===== RESULT TABLE HELPERS =====
function populateResultTables(data, summary) {
    // Update summary counts
    updateCount('matched-count', summary.matched_count);
    updateCount('unmatched-count', summary.unmatched_count);
    updateCount('notfound-count', summary.pair_not_found_count);
    updateCount('tab-matched-count', summary.matched_count);
    updateCount('tab-unmatched-count', summary.unmatched_count);
    updateCount('tab-notfound-count', summary.pair_not_found_count);

    // Populate table bodies
    renderTableBody('tbody-matched', data.matched, 'matched');
    renderTableBody('tbody-unmatched', data.unmatched, 'unmatched');
    renderTableBody('tbody-pair_not_found', data.pair_not_found, 'pair_not_found');
}

function updateCount(elementId, count) {
    const el = document.getElementById(elementId);
    if (el) el.textContent = count;
}

function renderTableBody(tbodyId, rows, type) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;

    if (!rows || rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="table-empty">Tidak ada data</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row, index) => {
        // Format created_at as full date + time (Waktu Proses)
        let waktuProses = '-';
        if (row.created_at) {
            const d = new Date(row.created_at);
            waktuProses = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        const sumberData = row.sumber_data || '-';
        const alasan = row.alasan_catatan || '-';
        const bsId = row.bank_statement_id || '-';
        const glId = row.internal_ledger_id || '-';
        const status = row.status_rekonsiliasi || type;

        const statusClass = type === 'matched' ? 'status-match'
            : type === 'unmatched' ? 'status-unmatch'
                : 'status-notfound';

        return `
            <tr>
                <td>${index + 1}</td>
                <td style="white-space:nowrap;">${waktuProses}</td>
                <td>${sumberData}</td>
                <td>${alasan}</td>
                <td>${bsId} / ${glId}</td>
                <td><span class="status-badge ${statusClass}">${status}</span></td>
            </tr>
        `;
    }).join('');
}

// ===== LOAD RESULTS DATA (auto-load on page load + refresh) =====
async function loadResultsData() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    try {
        const response = await fetch('/api/rekonsiliasi/results', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await response.json();
        if (data.success) {
            populateResultTables(data.data, data.summary);
            updatePieChart(data.summary);
        }
    } catch (error) {
        console.warn('Auto-load results failed:', error);
    }
}

// ===== UPDATE PIE CHART =====
function updatePieChart(summary) {
    const matched = summary.matched_count || 0;
    const unmatched = summary.unmatched_count || 0;
    const notfound = summary.pair_not_found_count || 0;
    const total = matched + unmatched + notfound;

    // Update legend percentages
    const matchedPct = total > 0 ? Math.round((matched / total) * 100) : 0;
    const unmatchedPct = total > 0 ? Math.round((unmatched / total) * 100) : 0;
    const notfoundPct = total > 0 ? Math.round((notfound / total) * 100) : 0;

    const elM = document.getElementById('pie-matched-pct');
    const elU = document.getElementById('pie-unmatched-pct');
    const elN = document.getElementById('pie-notfound-pct');
    if (elM) elM.textContent = matchedPct;
    if (elU) elU.textContent = unmatchedPct;
    if (elN) elN.textContent = notfoundPct;

    // Update chart data
    if (pieChartInstance) {
        pieChartInstance.data.datasets[0].data = [matched, unmatched, notfound];
        pieChartInstance.update();
    }
}

// ===== RESULT TABS =====
function initResultTabs() {
    const tabs = document.querySelectorAll('.result-tab');
    if (tabs.length === 0) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;

            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Update active panel
            document.querySelectorAll('.result-panel').forEach(p => p.classList.remove('active'));
            const targetPanel = document.getElementById(`panel-${targetTab}`);
            if (targetPanel) targetPanel.classList.add('active');
        });
    });
}

// ===== CHARTS =====
function initCharts() {
    const chartDefaults = {
        color: '#94a3b8',
        borderColor: 'rgba(99, 102, 241, 0.1)',
        font: { family: "'Inter', sans-serif" },
    };

    Chart.defaults.color = chartDefaults.color;
    Chart.defaults.font.family = chartDefaults.font.family;

    // Pie Chart — Reconciliation Status (dynamic, starts with zeroes)
    const pieEl = document.getElementById('reconciliation-pie-chart');
    if (pieEl) {
        pieChartInstance = new Chart(pieEl, {
            type: 'doughnut',
            data: {
                labels: ['Matched', 'Unmatched', 'Pair Not Found'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 11, 1)',
                    ],
                    borderWidth: 2,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1f35',
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(99, 102, 241, 0.2)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                                return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });
    }

    // Bar Chart — Monthly Trend (BI)
    const barEl = document.getElementById('monthly-trend-chart');
    if (barEl) {
        const months = ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb'];

        // destroy on hot reload (safe)
        if (window.monthlyTrendChart) window.monthlyTrendChart.destroy();

        window.monthlyTrendChart = new Chart(barEl, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Matched',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Unmatched',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20 },
                    },
                    tooltip: {
                        backgroundColor: '#1a1f35',
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(99, 102, 241, 0.2)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(99, 102, 241, 0.06)' },
                        ticks: { color: '#64748b' },
                    },
                    y: {
                        grid: { color: 'rgba(99, 102, 241, 0.06)' },
                        ticks: { color: '#64748b' },
                    },
                },
            },
        });
    }

    // Line Chart — Performance (BI)
    const lineEl = document.getElementById('performance-line-chart');
    if (lineEl) {
        const months = ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb'];

        if (window.performanceChart) window.performanceChart.destroy();

        window.performanceChart = new Chart(lineEl, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Akurasi (%)',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        borderColor: 'rgba(99, 102, 241, 1)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        pointBorderColor: '#1a1f35',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Waktu Proses (menit)',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        borderColor: 'rgba(139, 92, 246, 1)',
                        backgroundColor: 'rgba(139, 92, 246, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                        pointBorderColor: '#1a1f35',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20 },
                    },
                    tooltip: {
                        backgroundColor: '#1a1f35',
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(99, 102, 241, 0.2)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(99, 102, 241, 0.06)' },
                        ticks: { color: '#64748b' },
                    },
                    y: {
                        grid: { color: 'rgba(99, 102, 241, 0.06)' },
                        ticks: { color: '#64748b' },
                    },
                },
            },
        });
    }

    // Category Bar Chart (BI)
    const catBarEl = document.getElementById('category-bar-chart');
    if (catBarEl) {
        if (window.categoryChart) window.categoryChart.destroy();

        window.categoryChart = new Chart(catBarEl, {
            type: 'bar',
            data: {
                labels: ['-'],
                datasets: [{
                    label: 'Jumlah Selisih',
                    data: [0],
                    backgroundColor: ['rgba(99, 102, 241, 0.7)'],
                    borderColor: ['rgba(99, 102, 241, 1)'],
                    borderWidth: 1,
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1f35',
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(99, 102, 241, 0.2)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(99, 102, 241, 0.06)' },
                        ticks: { color: '#64748b' },
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' },
                    },
                },
            },
        });
    }
}

// ✅ FETCH BI REPORT DARI DB (summary + charts)
async function loadBiReportFromDb() {
    const hasBiPage =
        document.getElementById('monthly-trend-chart') ||
        document.getElementById('performance-line-chart') ||
        document.getElementById('category-bar-chart');

    if (!hasBiPage) return;

    try {
        const res = await fetch('/api/bi-report', {
            headers: { 'Accept': 'application/json' },
        });

        if (!res.ok) throw new Error(`BI API error: ${res.status}`);

        const data = await res.json();

        // ===== Update Summary Cards =====
        const elAcc = document.getElementById('bi-accuracy');
        const elAvg = document.getElementById('bi-avg-time');
        const elMonth = document.getElementById('bi-this-month');

        if (elAcc) elAcc.textContent = `${data.summary.accuracy_percent}%`;
        if (elAvg) elAvg.textContent = `${data.summary.avg_processing_min} mnt`;
        if (elMonth) elMonth.textContent = `${data.summary.recons_this_month}`;

        // ===== Update Monthly Trend Chart =====
        if (window.monthlyTrendChart) {
            window.monthlyTrendChart.data.labels = data.monthly.labels;
            if (window.monthlyTrendChart.data.datasets[0]) {
                window.monthlyTrendChart.data.datasets[0].data = data.monthly.matched;
            }
            if (window.monthlyTrendChart.data.datasets[1]) {
                window.monthlyTrendChart.data.datasets[1].data = data.monthly.unmatched;
            }
            window.monthlyTrendChart.update();
        }

        // ===== Update Performance Chart =====
        if (window.performanceChart) {
            window.performanceChart.data.labels = data.performance.labels;
            if (window.performanceChart.data.datasets[0]) {
                window.performanceChart.data.datasets[0].data = data.performance.accuracy;
            }
            if (window.performanceChart.data.datasets[1]) {
                window.performanceChart.data.datasets[1].data = data.performance.processing_min;
            }
            window.performanceChart.update();
        }

        // ===== Update Category Chart =====
        if (window.categoryChart) {
            window.categoryChart.data.labels = data.categories.labels;
            if (window.categoryChart.data.datasets[0]) {
                window.categoryChart.data.datasets[0].data = data.categories.values;
            }
            window.categoryChart.update();
        }
    } catch (err) {
        console.error('loadBiReportFromDb failed:', err);
    }
}

// ===== COUNT ANIMATION =====
function initCountAnimation() {
    const counters = document.querySelectorAll('[data-count]');
    counters.forEach((el) => {
        const target = parseInt(el.dataset.count, 10);
        const duration = 1500;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(current).toLocaleString('id-ID');
        }, 16);
    });
}

// ===== SETTINGS FORMS =====
function initSettingsForms() {
    const webhookForm = document.getElementById('webhook-form');
    if (webhookForm) {
        webhookForm.addEventListener('submit', (e) => {
            e.preventDefault();
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            }).fire({
                icon: 'success',
                title: 'Konfigurasi webhook berhasil disimpan',
            });
        });
    }

    // Settings forms (non-webhook)
    document.querySelectorAll('.settings-form').forEach((form) => {
        if (form.id === 'webhook-form') return;
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            }).fire({
                icon: 'success',
                title: 'Preferensi berhasil disimpan',
            });
        });
    });
}