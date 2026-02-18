@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Konfigurasi Sistem')

@section('content')
<div class="grid-2col">
    <!-- Webhook Configuration -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="display:inline;vertical-align:middle;margin-right:8px;">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                Konfigurasi Webhook
            </h3>
        </div>
        <div class="card-body">
            <form id="webhook-form" class="settings-form">
                <div class="form-group">
                    <label class="form-label" for="webhook-url">Webhook URL (n8n)</label>
                    <input type="url" id="webhook-url" class="form-input" value="https://kabel.web.id/webhook-test/Asisten-Tutup-Buku-Otomatis" placeholder="https://...">
                    <span class="form-hint">URL endpoint n8n untuk memproses data rekonsiliasi</span>
                </div>
                <div class="form-group">
                    <label class="form-label" for="webhook-method">Metode HTTP</label>
                    <select id="webhook-method" class="form-input">
                        <option value="POST" selected>POST</option>
                        <option value="PUT">PUT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="webhook-timeout">Timeout (detik)</label>
                    <input type="number" id="webhook-timeout" class="form-input" value="30" min="5" max="300">
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Konfigurasi
                </button>
            </form>
        </div>
    </div>

    <!-- Notification Preferences -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="display:inline;vertical-align:middle;margin-right:8px;">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                Preferensi Notifikasi
            </h3>
        </div>
        <div class="card-body">
            <form class="settings-form">
                <div class="form-group">
                    <div class="toggle-row">
                        <div>
                            <strong class="toggle-label">Notifikasi Sukses</strong>
                            <span class="form-hint">Tampilkan notifikasi saat rekonsiliasi berhasil</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="toggle-row">
                        <div>
                            <strong class="toggle-label">Notifikasi Error</strong>
                            <span class="form-hint">Tampilkan notifikasi saat terjadi kesalahan</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="toggle-row">
                        <div>
                            <strong class="toggle-label">Sound Effect</strong>
                            <span class="form-hint">Mainkan suara saat notifikasi muncul</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="notif-position">Posisi Notifikasi</label>
                    <select id="notif-position" class="form-input">
                        <option value="top-end" selected>Kanan Atas</option>
                        <option value="top-start">Kiri Atas</option>
                        <option value="bottom-end">Kanan Bawah</option>
                        <option value="bottom-start">Kiri Bawah</option>
                        <option value="center">Tengah</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Preferensi
                </button>
            </form>
        </div>
    </div>
</div>

<!-- About Section -->
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Tentang Aplikasi</h3>
    </div>
    <div class="card-body">
        <div class="about-info">
            <div class="about-row">
                <span class="about-label">Nama Aplikasi</span>
                <span class="about-value">Automated Financial Closing & Reconciliation Assistant</span>
            </div>
            <div class="about-row">
                <span class="about-label">Versi</span>
                <span class="about-value">1.0.0</span>
            </div>
            <div class="about-row">
                <span class="about-label">Framework</span>
                <span class="about-value">Laravel + Vite + Tailwind CSS v4</span>
            </div>
            <div class="about-row">
                <span class="about-label">Webhook Engine</span>
                <span class="about-value">n8n Automation</span>
            </div>
        </div>
    </div>
</div>
@endsection
