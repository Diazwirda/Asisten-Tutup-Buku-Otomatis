<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BiReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil dari Supabase (Postgres)
            $db = DB::connection('supabase');
            $table = 'reconciliation_log';

            $colCreated = 'created_at';
            $colStatus  = 'status_rekonsiliasi';

            // Status sesuai RekonsiliasiController kamu
            $STATUS_MATCH    = 'match';
            $STATUS_UNMATCH  = 'unmatch';
            $STATUS_NOTFOUND = 'pair not found';

            // optional: rata-rata waktu proses (kalau kolom ada)
            $colProcessing = 'processing_seconds'; // ganti kalau di supabase beda

            // =============================
            // 1) SUMMARY
            // =============================
            $total = (int) $db->table($table)->count();

            $matched     = (int) $db->table($table)->where($colStatus, $STATUS_MATCH)->count();
            $unmatched   = (int) $db->table($table)->where($colStatus, $STATUS_UNMATCH)->count();
            $pairNotFound = (int) $db->table($table)->where($colStatus, $STATUS_NOTFOUND)->count();

            $accuracy = $total > 0 ? round(($matched / $total) * 100, 1) : 0.0;

            $thisMonthCount = (int) $db->table($table)
                ->whereBetween($colCreated, [now()->startOfMonth(), now()->endOfMonth()])
                ->count();

            $avgProcessingMinutes = 0.0;
            try {
                $avgSec = (float) ($db->table($table)->avg($colProcessing) ?? 0);
                $avgProcessingMinutes = $avgSec > 0 ? round($avgSec / 60, 1) : 0.0;
            } catch (\Throwable $e) {
                $avgProcessingMinutes = 0.0;
            }

            $summary = [
                'accuracy_percent'    => $accuracy,
                'avg_processing_min'  => $avgProcessingMinutes,
                'recons_this_month'   => $thisMonthCount,
                'total'               => $total,
                'matched'             => $matched,
                'unmatched'           => $unmatched,
                'pair_not_found'      => $pairNotFound,
            ];

            // =============================
            // 2) SIAPKAN 12 BULAN TERAKHIR (PADDING)
            // =============================
            $startDate = now()->subMonths(11)->startOfMonth();

            // month_key: YYYY-MM (contoh 2026-02)
            // month_label: Mon (contoh Feb)
            $monthKeyExpr   = "to_char($colCreated, 'YYYY-MM')";
            $monthLabelExpr = "to_char($colCreated, 'Mon')";

            // Generate 12 bulan terakhir agar output selalu per bulan (meski kosong)
            $months = [];
            $start = Carbon::now()->subMonths(11)->startOfMonth();
            for ($i = 0; $i < 12; $i++) {
                $d = $start->copy()->addMonths($i);
                $key = $d->format('Y-m'); // YYYY-MM
                // label pakai Carbon biar konsisten (kalau kamu mau Indonesia, set locale app)
                $label = $d->translatedFormat('M'); // Jan, Feb, Mar...
                $months[$key] = [
                    'label' => $label,
                    'matched' => 0,
                    'unmatched' => 0,
                    'total' => 0,
                    'avg_processing_seconds' => 0.0,
                ];
            }

            // =============================
            // 3) MONTHLY TREND (12 BULAN) -> merge ke months
            // =============================
            $monthlyRows = $db->table($table)
                ->selectRaw("$monthKeyExpr as month_key, $monthLabelExpr as month_label")
                ->selectRaw("SUM(CASE WHEN $colStatus = ? THEN 1 ELSE 0 END) as matched_count", [$STATUS_MATCH])
                ->selectRaw("SUM(CASE WHEN $colStatus = ? THEN 1 ELSE 0 END) as unmatched_count", [$STATUS_UNMATCH])
                ->where($colCreated, '>=', $startDate)
                ->groupBy('month_key', 'month_label')
                ->orderBy('month_key')
                ->get();

            foreach ($monthlyRows as $r) {
                $k = (string) $r->month_key;
                if (isset($months[$k])) {
                    $months[$k]['matched']   = (int) $r->matched_count;
                    $months[$k]['unmatched'] = (int) $r->unmatched_count;
                }
            }

            $monthly = [
                'labels'   => array_values(array_map(fn ($m) => $m['label'], $months)),
                'matched'  => array_values(array_map(fn ($m) => $m['matched'], $months)),
                'unmatched'=> array_values(array_map(fn ($m) => $m['unmatched'], $months)),
            ];

            // =============================
            // 4) PERFORMANCE -> accuracy & waktu proses per bulan (merge)
            // =============================
            $perfRows = $db->table($table)
                ->selectRaw("$monthKeyExpr as month_key, $monthLabelExpr as month_label")
                ->selectRaw("COUNT(*) as total_count")
                ->selectRaw("SUM(CASE WHEN $colStatus = ? THEN 1 ELSE 0 END) as matched_count", [$STATUS_MATCH])
                ->where($colCreated, '>=', $startDate)
                ->groupBy('month_key', 'month_label')
                ->orderBy('month_key')
                ->get();

            foreach ($perfRows as $r) {
                $k = (string) $r->month_key;
                if (isset($months[$k])) {
                    $months[$k]['total'] = (int) $r->total_count;

                    // Sinkronkan matched dari perf juga (optional, tapi aman)
                    $months[$k]['matched'] = (int) $r->matched_count;
                    // Unmatched sudah ada dari monthlyRows; kalau mau lengkap, bisa hitung:
                    // $months[$k]['unmatched'] = max(0, $months[$k]['total'] - $months[$k]['matched']);
                }
            }

            // Waktu proses per bulan (jika kolom ada)
            try {
                $perfTimeRows = $db->table($table)
                    ->selectRaw("$monthKeyExpr as month_key, $monthLabelExpr as month_label")
                    ->selectRaw("AVG($colProcessing) as avg_processing_seconds")
                    ->where($colCreated, '>=', $startDate)
                    ->groupBy('month_key', 'month_label')
                    ->orderBy('month_key')
                    ->get();

                foreach ($perfTimeRows as $r) {
                    $k = (string) $r->month_key;
                    if (isset($months[$k])) {
                        $months[$k]['avg_processing_seconds'] = (float) ($r->avg_processing_seconds ?? 0);
                    }
                }
            } catch (\Throwable $e) {
                // keep zeros
            }

            $perfAccuracy = [];
            $perfTimeMin  = [];

            foreach ($months as $m) {
                $t = (int) $m['total'];
                $mch = (int) $m['matched'];

                $acc = $t > 0 ? round(($mch / $t) * 100, 1) : 0.0;
                $min = $m['avg_processing_seconds'] > 0 ? round($m['avg_processing_seconds'] / 60, 1) : 0.0;

                $perfAccuracy[] = $acc;
                $perfTimeMin[]  = $min;
            }

            $performance = [
                'labels'         => $monthly['labels'],
                'accuracy'       => $perfAccuracy,
                'processing_min' => $perfTimeMin,
            ];

            // =============================
            // 5) CATEGORY DISTRIBUTION (fallback: group by status)
            // =============================
            $categoryRows = $db->table($table)
                ->selectRaw("$colStatus as category")
                ->selectRaw("COUNT(*) as total_count")
                ->groupBy($colStatus)
                ->orderByDesc('total_count')
                ->limit(6)
                ->get();

            $categories = [
                'labels' => $categoryRows->pluck('category')->values(),
                'values' => $categoryRows->pluck('total_count')->map(fn ($v) => (int) $v)->values(),
            ];

            return response()->json([
                'summary'     => $summary,
                'monthly'     => $monthly,
                'performance' => $performance,
                'categories'  => $categories,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}