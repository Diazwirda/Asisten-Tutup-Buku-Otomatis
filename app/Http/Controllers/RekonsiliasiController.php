<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RekonsiliasiController extends Controller
{
    /**
     * Process reconciliation: send files to n8n webhook.
     */
    public function process(Request $request)
    {
        $request->validate([
            'general_ledger' => 'required|file|mimes:csv,xlsx,xls',
            'bank_statement' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        $webhookUrl = 'https://kabel.web.id/webhook-test/Asisten-Tutup-Buku-Otomatis';

        try {
            $response = Http::withoutVerifying()->timeout(120)
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
    }

    /**
     * Fetch reconciliation results from Supabase database.
     */
    public function getResults()
    {
        try {
            $db = DB::connection('supabase');

            // Get reconciliation log grouped by status
            $matched = $db->table('reconciliation_log')
                ->where('status_rekonsiliasi', 'match')
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get();

            $unmatched = $db->table('reconciliation_log')
                ->where('status_rekonsiliasi', 'unmatch')
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get();

            $pairNotFound = $db->table('reconciliation_log')
                ->where('status_rekonsiliasi', 'pair not found')
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get();

            // Get bank statement and internal ledger data for detail
            $bankStatement = $db->table('bank_statement')
                ->orderBy('Tanggal Bank', 'desc')
                ->limit(500)
                ->get();

            $internalLedger = $db->table('internal_ledger')
                ->orderBy('Tanggal', 'desc')
                ->limit(500)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'matched' => $matched,
                    'unmatched' => $unmatched,
                    'pair_not_found' => $pairNotFound,
                ],
                'detail' => [
                    'bank_statement' => $bankStatement,
                    'internal_ledger' => $internalLedger,
                ],
                'summary' => [
                    'matched_count' => count($matched),
                    'unmatched_count' => count($unmatched),
                    'pair_not_found_count' => count($pairNotFound),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari database: ' . $e->getMessage(),
            ], 500);
        }
    }
}
