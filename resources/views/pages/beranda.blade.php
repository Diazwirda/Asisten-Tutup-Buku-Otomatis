@extends('layouts.app')

@section('title', 'Beranda')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Dashboard Overview')

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-banner-content">
        <h2 class="welcome-title">Selamat Datang di <span class="text-gradient">Recon Assistant</span></h2>
        <p class="welcome-text">Automated Financial Closing & Reconciliation Assistant membantu divisi FAT melakukan rekonsiliasi data secara otomatis, cepat, dan akurat.</p>
        <a href="/rekonsiliasi" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            Mulai Rekonsiliasi
        </a>
    </div>
    <div class="welcome-banner-visual">
        <div class="visual-circle visual-circle-1"></div>
        <div class="visual-circle visual-circle-2"></div>
        <div class="visual-circle visual-circle-3"></div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value" data-count="1248">0</span>
            <span class="stat-label">Total Transaksi</span>
        </div>
        <div class="stat-trend stat-trend-up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
            </svg>
            +12.5%
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value" data-count="897">0</span>
            <span class="stat-label">Matched</span>
        </div>
        <div class="stat-trend stat-trend-up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
            </svg>
            +8.2%
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value" data-count="351">0</span>
            <span class="stat-label">Unmatched</span>
        </div>
        <div class="stat-trend stat-trend-down">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
            </svg>
            -3.1%
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value" data-count="24">0</span>
            <span class="stat-label">Pending Review</span>
        </div>
        <div class="stat-trend stat-trend-neutral">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            0%
        </div>
    </div>
</div>

<!-- Quick Actions & Activity -->
<div class="grid-2col">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aksi Cepat</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="/rekonsiliasi" class="quick-action-item">
                    <div class="quick-action-icon qa-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    </div>
                    <div>
                        <strong>Upload & Rekonsiliasi</strong>
                        <span>Upload GL & Bank Statement</span>
                    </div>
                </a>
                <a href="/laporan-bi" class="quick-action-item">
                    <div class="quick-action-icon qa-purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <div>
                        <strong>Laporan BI</strong>
                        <span>Lihat dashboard analitik</span>
                    </div>
                </a>
                <a href="/pengaturan" class="quick-action-item">
                    <div class="quick-action-icon qa-teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4"/>
                        </svg>
                    </div>
                    <div>
                        <strong>Pengaturan</strong>
                        <span>Konfigurasi webhook & notifikasi</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aktivitas Terbaru</h3>
        </div>
        <div class="card-body">
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-dot activity-dot-green"></div>
                    <div class="activity-info">
                        <span class="activity-text">Rekonsiliasi Januari 2026 selesai</span>
                        <span class="activity-time">2 jam lalu</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot activity-dot-blue"></div>
                    <div class="activity-info">
                        <span class="activity-text">File GL_Jan2026.xlsx diunggah</span>
                        <span class="activity-time">3 jam lalu</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot activity-dot-blue"></div>
                    <div class="activity-info">
                        <span class="activity-text">File BankStmt_Jan2026.csv diunggah</span>
                        <span class="activity-time">3 jam lalu</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot activity-dot-yellow"></div>
                    <div class="activity-info">
                        <span class="activity-text">24 transaksi perlu review manual</span>
                        <span class="activity-time">5 jam lalu</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot activity-dot-green"></div>
                    <div class="activity-info">
                        <span class="activity-text">Rekonsiliasi Desember 2025 selesai</span>
                        <span class="activity-time">1 hari lalu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
