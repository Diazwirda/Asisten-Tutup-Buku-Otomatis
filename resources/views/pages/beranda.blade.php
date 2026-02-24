@extends('layouts.app')

@section('title', 'Beranda')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Dashboard Overview')

@section('content')
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-banner-content">
            <h2 class="welcome-title">
                Selamat Datang di <span class="text-gradient">Recon Assistant</span>
            </h2>
            <p class="welcome-text">
                Automated Financial Closing & Reconciliation Assistant membantu divisi FAT melakukan rekonsiliasi data secara otomatis, cepat, dan akurat.
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Transaksi -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>

            <div class="stat-info">
                <span class="stat-value">{{ number_format($kpi['total_transaksi'] ?? 0) }}</span>
                <span class="stat-label">Total Transaksi</span>
            </div>

            <div class="stat-trend stat-trend-up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                +12.5%
            </div>
        </div>

        <!-- Matched -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>

            <div class="stat-info">
                <span class="stat-value">{{ number_format($kpi['matched'] ?? 0) }}</span>
                <span class="stat-label">Matched</span>
            </div>

            <div class="stat-trend stat-trend-up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                +8.2%
            </div>
        </div>

        <!-- Unmatched -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>

            <div class="stat-info">
                <span class="stat-value">{{ number_format($kpi['unmatched'] ?? 0) }}</span>
                <span class="stat-label">Unmatched</span>
            </div>

            <div class="stat-trend stat-trend-down">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
                </svg>
                -3.1%
            </div>
        </div>

        <!-- Pending Review -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>

            <div class="stat-info">
                <span class="stat-value">{{ number_format($kpi['pending_review'] ?? 0) }}</span>
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
@endsection