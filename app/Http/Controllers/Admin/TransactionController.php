<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Withdraw;

class TransactionController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['user', 'platform'])
            ->latest()->paginate(50);
        return view('Admin.Transactions.Index', compact('transaksis'));
    }

    public function withdraws()
    {
        $withdraws = Withdraw::with('developer')->latest()->paginate(50);
        return view('Admin.Transactions.Withdraws', compact('withdraws'));
    }
}
