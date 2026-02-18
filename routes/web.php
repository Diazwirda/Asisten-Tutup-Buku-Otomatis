<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('pages.beranda');
});

Route::get('/rekonsiliasi', function () {
    return view('pages.rekonsiliasi');
});

Route::get('/laporan-bi', function () {
    return view('pages.laporan-bi');
});

Route::get('/pengaturan', function () {
    return view('pages.pengaturan');
});

// Proxy endpoint to forward files to n8n webhook (avoids CORS)
Route::post('/api/rekonsiliasi', function (Request $request) {
    $request->validate([
        'general_ledger' => 'required|file|mimes:csv,xlsx,xls',
        'bank_statement' => 'required|file|mimes:csv,xlsx,xls',
    ]);

    $webhookUrl = 'https://kabel.web.id/webhook-test/Asisten-Tutup-Buku-Otomatis';

    try {
        $response = Http::withoutVerifying()->timeout(60)
            ->attach('general_ledger', file_get_contents($request->file('general_ledger')->getRealPath()), $request->file('general_ledger')->getClientOriginalName())
            ->attach('bank_statement', file_get_contents($request->file('bank_statement')->getRealPath()), $request->file('bank_statement')->getClientOriginalName())
            ->post($webhookUrl);

        return response()->json([
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $response->body(),
        ], $response->successful() ? 200 : 502);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghubungi webhook: ' . $e->getMessage(),
        ], 500);
    }
});
