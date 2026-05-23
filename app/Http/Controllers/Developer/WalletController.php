<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Support\Facades\Session;

class WalletController extends Controller
{
    public function index()
    {
        $devId  = Session::get('user_id');
        $wallet = Wallet::firstOrCreate(['dev_id' => $devId], ['saldo' => 0]);
        $mutations = $wallet->transactions()->latest()->paginate(30);

        return view('Developer.Wallet', compact('wallet', 'mutations'));
    }
}
