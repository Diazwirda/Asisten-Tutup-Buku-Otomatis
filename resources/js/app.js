import Swal from 'sweetalert2';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

// ===== DOM READY =====
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDateDisplay();
    initFileUploads();
    initProcessButton();
    initCharts();
    initCountAnimation();
    initSettingsForms();
});

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

        // Show loading
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) loadingOverlay.style.display = 'flex';

        // Build FormData
        const formData = new FormData();
        formData.append('general_ledger', glInput.files[0]);
        formData.append('bank_statement', bsInput.files[0]);

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const response = await fetch('/api/rekonsiliasi', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            // Hide loading
            if (loadingOverlay) loadingOverlay.style.display = 'none';

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Rekonsiliasi Berhasil!',
                    html: `<p style="color:#94a3b8;">Data telah berhasil dikirim ke server n8n untuk diproses.</p>
                           <p style="color:#64748b;font-size:0.85rem;margin-top:8px;">Response: ${String(result.data).substring(0, 200)}</p>`,
                    background: '#1a1f35',
                    color: '#f1f5f9',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'Tutup',
                });
            } else {
                throw new Error(result.message || `HTTP Error: ${result.status}`);
            }
        } catch (error) {
            // Hide loading
            if (loadingOverlay) loadingOverlay.style.display = 'none';

            Swal.fire({
                icon: 'error',
                title: 'Proses Gagal',
                html: `<p style="color:#94a3b8;">Terjadi kesalahan saat mengirim data ke webhook.</p>
                       <p style="color:#ef4444;font-size:0.85rem;margin-top:8px;">${error.message}</p>`,
                background: '#1a1f35',
                color: '#f1f5f9',
                confirmButtonColor: '#6366f1',
                confirmButtonText: 'Coba Lagi',
            });
        }
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

    // Pie Chart — Reconciliation Status
    const pieEl = document.getElementById('reconciliation-pie-chart');
    if (pieEl) {
        new Chart(pieEl, {
            type: 'doughnut',
            data: {
                labels: ['Matched', 'Unmatched', 'Pending'],
                datasets: [{
                    data: [72, 20, 8],
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
                            label: (ctx) => `${ctx.label}: ${ctx.parsed}%`,
                        },
                    },
                },
            },
        });
    }

    // Bar Chart — Monthly Trend
    const barEl = document.getElementById('monthly-trend-chart');
    if (barEl) {
        const months = ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb'];
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Matched',
                        data: [820, 850, 900, 870, 920, 950, 880, 910, 940, 970, 897, 920],
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Unmatched',
                        data: [180, 150, 120, 160, 100, 80, 130, 110, 90, 60, 351, 100],
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

    // Line Chart — Performance
    const lineEl = document.getElementById('performance-line-chart');
    if (lineEl) {
        const months = ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb'];
        new Chart(lineEl, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Akurasi (%)',
                        data: [88, 90, 91, 89, 93, 94, 92, 93, 95, 96, 95, 95.2],
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
                        data: [5.2, 4.8, 4.5, 4.7, 4.0, 3.8, 3.5, 3.2, 3.0, 2.8, 2.5, 2.4],
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

    // Category Bar Chart
    const catBarEl = document.getElementById('category-bar-chart');
    if (catBarEl) {
        new Chart(catBarEl, {
            type: 'bar',
            data: {
                labels: ['Biaya Operasional', 'Pendapatan', 'Pajak', 'Transfer Antar Rek.', 'Gaji & Tunjangan', 'Lainnya'],
                datasets: [{
                    label: 'Jumlah Selisih',
                    data: [45, 32, 28, 18, 12, 8],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(20, 184, 166, 0.7)',
                    ],
                    borderColor: [
                        'rgba(99, 102, 241, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(20, 184, 166, 1)',
                    ],
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
