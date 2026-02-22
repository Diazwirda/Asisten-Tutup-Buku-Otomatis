@extends('layouts.app')

@section('title', 'Rekonsiliasi')
@section('page-title', 'Rekonsiliasi')
@section('page-subtitle', 'Upload & Proses Rekonsiliasi Data')

@section('content')
<!-- Upload Section -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="display:inline;vertical-align:middle;margin-right:8px;">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Upload Dokumen
        </h3>
        <span class="card-badge">Step 1</span>
    </div>
    <div class="card-body">
        <div class="upload-grid">
            <!-- General Ledger Upload -->
            <div class="upload-zone" id="gl-upload-zone" data-target="gl-file-input">
                <input type="file" id="gl-file-input" accept=".csv,.xlsx,.xls" hidden>
                <div class="upload-icon upload-icon-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <h4 class="upload-title">General Ledger</h4>
                <p class="upload-subtitle">Buku Besar Internal</p>
                <p class="upload-hint">Drag & drop atau <span class="upload-browse">browse</span></p>
                <p class="upload-format">.csv, .xlsx</p>
                <div class="upload-file-info" id="gl-file-info" style="display:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span id="gl-file-name"></span>
                    <button class="upload-remove" data-target="gl" title="Hapus file">&times;</button>
                </div>
            </div>

            <!-- Bank Statement Upload -->
            <div class="upload-zone" id="bs-upload-zone" data-target="bs-file-input">
                <input type="file" id="bs-file-input" accept=".csv,.xlsx,.xls" hidden>
                <div class="upload-icon upload-icon-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <h4 class="upload-title">Bank Statement</h4>
                <p class="upload-subtitle">Rekening Koran Bank</p>
                <p class="upload-hint">Drag & drop atau <span class="upload-browse">browse</span></p>
                <p class="upload-format">.csv, .xlsx</p>
                <div class="upload-file-info" id="bs-file-info" style="display:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span id="bs-file-name"></span>
                    <button class="upload-remove" data-target="bs" title="Hapus file">&times;</button>
                </div>
            </div>
        </div>

        <!-- Process Button -->
        <div class="upload-actions">
            <button id="btn-process" class="btn btn-primary btn-lg" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                Mulai Proses Rekonsiliasi
            </button>
            <p class="upload-note">Upload kedua file terlebih dahulu untuk memulai proses rekonsiliasi.</p>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="display:inline;vertical-align:middle;margin-right:8px;">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                <path d="M22 12A10 10 0 0 0 12 2v10z"/>
            </svg>
            Status Rekonsiliasi
        </h3>
        <span class="card-badge card-badge-green">Live Data</span>
    </div>
    <div class="card-body">
        <div class="chart-container-pie">
            <canvas id="reconciliation-pie-chart"></canvas>
        </div>
        <div class="chart-legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #10b981;"></span>
                <span>Matched — <span id="pie-matched-pct">0</span>%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ef4444;"></span>
                <span>Unmatched — <span id="pie-unmatched-pct">0</span>%</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>Pair Not Found — <span id="pie-notfound-pct">0</span>%</span>
            </div>
        </div>
    </div>
</div>

<!-- Results Section (Always visible) -->
<div id="results-section" class="card" style="margin-top: 1.5rem;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <h3 class="card-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="display:inline;vertical-align:middle;margin-right:8px;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            Hasil Rekonsiliasi
        </h3>
        <div style="display:flex;align-items:center;gap:8px;">
            <button id="btn-refresh-results" class="btn-refresh" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="display:inline;vertical-align:middle;">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    <polyline points="21 3 21 9 15 9"/>
                </svg>
                Refresh Data
            </button>
            <span class="card-badge" id="results-badge">Step 2</span>
        </div>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="results-summary">
            <div class="result-summary-item result-summary-match">
                <div class="result-summary-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <span class="result-summary-count" id="matched-count">0</span>
                    <span class="result-summary-label">Matched</span>
                </div>
            </div>
            <div class="result-summary-item result-summary-unmatch">
                <div class="result-summary-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <span class="result-summary-count" id="unmatched-count">0</span>
                    <span class="result-summary-label">Unmatched</span>
                </div>
            </div>
            <div class="result-summary-item result-summary-notfound">
                <div class="result-summary-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                </div>
                <div>
                    <span class="result-summary-count" id="notfound-count">0</span>
                    <span class="result-summary-label">Pair Not Found</span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="result-tabs">
            <button class="result-tab active" data-tab="matched">
                <span class="tab-dot tab-dot-green"></span>
                Matched
                <span class="tab-count" id="tab-matched-count">0</span>
            </button>
            <button class="result-tab" data-tab="unmatched">
                <span class="tab-dot tab-dot-red"></span>
                Unmatched
                <span class="tab-count" id="tab-unmatched-count">0</span>
            </button>
            <button class="result-tab" data-tab="pair_not_found">
                <span class="tab-dot tab-dot-yellow"></span>
                Pair Not Found
                <span class="tab-count" id="tab-notfound-count">0</span>
            </button>
        </div>

        <!-- Table Panels -->
        <div class="result-panel active" id="panel-matched">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu Proses</th>
                            <th>Sumber Data</th>
                            <th>Alasan / Catatan</th>
                            <th>BS ID / GL ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-matched">
                        <tr><td colspan="6" class="table-empty">Klik Refresh Data untuk memuat data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="result-panel" id="panel-unmatched">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu Proses</th>
                            <th>Sumber Data</th>
                            <th>Alasan / Catatan</th>
                            <th>BS ID / GL ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-unmatched">
                        <tr><td colspan="6" class="table-empty">Klik Refresh Data untuk memuat data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="result-panel" id="panel-pair_not_found">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu Proses</th>
                            <th>Sumber Data</th>
                            <th>Alasan / Catatan</th>
                            <th>BS ID / GL ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-pair_not_found">
                        <tr><td colspan="6" class="table-empty">Klik Refresh Data untuk memuat data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
