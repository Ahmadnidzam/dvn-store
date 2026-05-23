<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Wallet;
use App\Models\Withdraw;
use App\Services\MidtransPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class WithdrawController extends Controller
{
    public function index()
    {
        $devId = Session::get('user_id');
        $wallet = Wallet::firstOrCreate(['dev_id' => $devId], ['saldo' => 0]);
        $withdraws = Withdraw::where('dev_id', $devId)->latest()->paginate(20);
        $minWithdraw = (int) config('dvnstore.min_withdraw');
        $irisEnabled = (bool) config('dvnstore.iris.enabled');

        return view('Developer.Withdraws.Index', compact('wallet', 'withdraws', 'minWithdraw', 'irisEnabled'));
    }

    public function create()
    {
        if (!config('dvnstore.iris.enabled')) {
            return redirect()->route('developer.withdraws.index')
                ->with('error', 'Fitur withdraw sedang dipersiapkan. Silakan coba lagi nanti.');
        }

        $devId = Session::get('user_id');
        $dev = Pengguna::with('developerProfile')->find($devId);
        $wallet = Wallet::firstOrCreate(['dev_id' => $devId], ['saldo' => 0]);
        $minWithdraw = (int) config('dvnstore.min_withdraw');

        return view('Developer.Withdraws.Create', compact('dev', 'wallet', 'minWithdraw'));
    }

    public function store(Request $request, MidtransPayoutService $iris)
    {
        if (!config('dvnstore.iris.enabled')) {
            return redirect()->route('developer.withdraws.index')
                ->with('error', 'Fitur withdraw sedang dipersiapkan. Silakan coba lagi nanti.');
        }

        $min   = (int) config('dvnstore.min_withdraw');
        $devId = Session::get('user_id');

        $request->validate([
            'amount' => "required|integer|min:{$min}",
        ]);

        $dev = Pengguna::with('developerProfile')->findOrFail($devId);
        if (!$dev->developerProfile) {
            return back()->with('error', 'Lengkapi profil developer terlebih dahulu.');
        }

        try {
            $withdraw = DB::transaction(function () use ($request, $dev, $devId) {
                $wallet = Wallet::where('dev_id', $devId)->lockForUpdate()->firstOrFail();
                if ($wallet->saldo < $request->amount) {
                    throw new \RuntimeException('Saldo tidak cukup.');
                }

                $w = Withdraw::create([
                    'dev_id' => $dev->id,
                    'amount' => (int) $request->amount,
                    'bank_snapshot' => [
                        'bank_name'           => $dev->developerProfile->bank_name,
                        'bank_account_number' => $dev->developerProfile->bank_account_number,
                        'bank_account_holder' => $dev->developerProfile->bank_account_holder,
                    ],
                    'status' => 'pending',
                ]);

                $wallet->debit((int) $request->amount, 'Withdraw #' . $w->id);
                return $w;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $result = $iris->createPayout($withdraw);
        $withdraw->update([
            'iris_payout_reference_no' => $result['reference_no'],
            'status'                   => $result['ok'] ? 'processing' : 'failed',
            'failure_reason'           => $result['ok'] ? null : 'IRIS gagal request payout.',
            'iris_response'            => $result['raw'],
        ]);

        // Kalau gagal request, kembalikan saldo
        if (!$result['ok']) {
            $wallet = Wallet::where('dev_id', $devId)->firstOrFail();
            $wallet->credit($withdraw->amount, 'Refund: withdraw #' . $withdraw->id . ' gagal');
        }

        return redirect()->route('developer.withdraws.index')
            ->with($result['ok'] ? 'success' : 'error', $result['ok']
                ? 'Permintaan withdraw dikirim. Menunggu IRIS memproses.'
                : 'Withdraw gagal. Saldo dikembalikan.');
    }
}
