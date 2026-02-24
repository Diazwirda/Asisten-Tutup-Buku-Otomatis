<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;

class DashboardController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $kpi = $supabase->getDashboardKpi();

        return view('pages.beranda', compact('kpi'));
    }
}