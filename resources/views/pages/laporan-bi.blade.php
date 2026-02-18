@extends('layouts.app')

@section('title', 'Laporan BI')
@section('page-title', 'Laporan BI')
@section('page-subtitle', 'Business Intelligence & Analytics')

@section('content')
<!-- Summary Stats -->
<div class="stats-grid stats-grid-3">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">95.2%</span>
            <span class="stat-label">Tingkat Akurasi</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">2.4 mnt</span>
            <span class="stat-label">Rata-rata Waktu Proses</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">12</span>
            <span class="stat-label">Rekonsiliasi Bulan Ini</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid-2col">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tren Rekonsiliasi Bulanan</h3>
            <span class="card-badge">12 Bulan</span>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="monthly-trend-chart"></canvas>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Performa Rekonsiliasi</h3>
            <span class="card-badge card-badge-green">Live</span>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="performance-line-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Bar Chart -->
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Distribusi Selisih per Kategori</h3>
        <span class="card-badge">Detail</span>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="category-bar-chart"></canvas>
        </div>
    </div>
</div>
@endsection
