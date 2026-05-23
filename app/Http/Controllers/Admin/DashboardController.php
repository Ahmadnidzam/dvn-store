<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Platform;
use App\Models\Transaksi;
use App\Models\Withdraw;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers       = Pengguna::where('role', 'user')->count();
        $totalDevelopers  = Pengguna::where('role', 'developer')->count();
        $totalGames       = Platform::where('category', 'game')->count();
        $totalApps        = Platform::where('category', 'app')->count();
        $totalRevenue     = (int) Transaksi::where('status', 'paid')->sum('platform_fee'); // pendapatan platform
        $totalGMV         = (int) Transaksi::where('status', 'paid')->where('tipe', 'purchase')->sum('amount');
        $pendingWithdraws = Withdraw::whereIn('status', ['pending', 'processing'])->count();
        $blockedUsers     = Pengguna::where('status', 'blocked')->count();
        $takenDownPlatforms = Platform::where('is_taken_down', true)->count();

        $recentTransactions = Transaksi::with(['user', 'platform'])
            ->where('status', 'paid')->latest('paid_at')->limit(10)->get();

        return view('Admin.Dashboard', compact(
            'totalUsers', 'totalDevelopers', 'totalGames', 'totalApps',
            'totalRevenue', 'totalGMV', 'pendingWithdraws', 'blockedUsers',
            'takenDownPlatforms', 'recentTransactions'
        ));
    }
}
