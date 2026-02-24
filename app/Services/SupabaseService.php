<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SupabaseService
{
    private string $baseUrl;
    private string $anonKey;
    private ?string $caBundlePath;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) env('SUPABASE_URL'), '/');
        $this->anonKey = (string) env('SUPABASE_ANON_KEY');

        // OPTIONAL: untuk Windows/XAMPP yang sering SSL issue.
        // Isi path ini lewat .env jika perlu (lihat bagian .env di bawah).
        $this->caBundlePath = env('SUPABASE_CA_BUNDLE') ? (string) env('SUPABASE_CA_BUNDLE') : null;
    }

    /**
     * Default KPI agar UI tidak blank ketika Supabase error/timeout.
     */
    private function defaultKpi(): array
    {
        return [
            'total_transaksi' => 0,
            'matched' => 0,
            'unmatched' => 0,
            'pending_review' => 0,
        ];
    }

    public function getDashboardKpi(): array
    {
        if (empty($this->baseUrl) || empty($this->anonKey)) {
            return $this->defaultKpi();
        }

        $url = $this->baseUrl . '/rest/v1/dashboard_kpi';

        $options = [];
        // Jika diset, Guzzle akan pakai CA bundle ini untuk verifikasi SSL
        if (!empty($this->caBundlePath)) {
            $options['verify'] = $this->caBundlePath;
        }

        try {
            $response = Http::withOptions($options)
                ->withHeaders([
                    'apikey' => $this->anonKey,
                    'Authorization' => 'Bearer ' . $this->anonKey,
                    'Accept' => 'application/json',
                ])
                ->timeout((int) env('SUPABASE_TIMEOUT', 20))
                ->retry((int) env('SUPABASE_RETRY', 2), (int) env('SUPABASE_RETRY_SLEEP', 250))
                ->get($url, [
                    'select' => '*',
                    'limit' => 1,
                ]);

            if (!$response->successful()) {
                return $this->defaultKpi();
            }

            $json = $response->json();
            $row = is_array($json) ? ($json[0] ?? []) : [];

            // Merge agar semua key KPI selalu ada
            return array_merge($this->defaultKpi(), is_array($row) ? $row : []);
        } catch (ConnectionException $e) {
            // timeout / SSL / network error
            return $this->defaultKpi();
        } catch (\Throwable $e) {
            return $this->defaultKpi();
        }
    }
}