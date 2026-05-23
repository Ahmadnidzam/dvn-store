<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Platform;
use App\Models\Review;
use App\Models\Transaksi;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $devId = Session::get('user_id');

        $totalPlatforms  = Platform::where('dev_id', $devId)->count();
        $totalPublished  = Platform::where('dev_id', $devId)->where('is_published', true)->count();
        $totalDownloads  = Download::whereHas('platform', fn($q) => $q->where('dev_id', $devId))->count();
        $totalReviews    = Review::whereHas('platform', fn($q) => $q->where('dev_id', $devId))->count();

        $totalRevenue = (int) Transaksi::whereHas('platform', fn($q) => $q->where('dev_id', $devId))
            ->where('tipe', 'purchase')
            ->where('status', 'paid')
            ->sum('net_amount');

        $wallet = Wallet::firstOrCreate(['dev_id' => $devId], ['saldo' => 0]);

        $topPlatforms = Platform::where('dev_id', $devId)
            ->withCount(['downloads', 'reviews'])
            ->orderByDesc('rating')
            ->limit(5)->get();

        $salesPerMonth = Transaksi::whereHas('platform', fn($q) => $q->where('dev_id', $devId))
            ->where('tipe', 'purchase')->where('status', 'paid')
            ->select(DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as bulan"), DB::raw('SUM(net_amount) as total'))
            ->groupBy('bulan')->orderBy('bulan')->limit(12)->get();

        return view('Developer.Dashboard', compact(
            'totalPlatforms', 'totalPublished', 'totalDownloads', 'totalReviews',
            'totalRevenue', 'wallet', 'topPlatforms', 'salesPerMonth'
        ));
    }
}
