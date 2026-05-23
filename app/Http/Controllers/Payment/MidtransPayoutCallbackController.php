<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransPayoutCallbackController extends Controller
{
    /**
     * Webhook Midtrans IRIS untuk update status payout.
     * Pasang URL ini di IRIS Dashboard → Settings → Notification URL.
     *
     * IRIS mengirim field minimal: reference_no, status, updated_at, ...
     */
    public function notify(Request $request)
    {
        $refNo  = $request->input('reference_no');
        $status = strtolower((string) $request->input('status'));
        if (!$refNo) {
            return response()->json(['ok' => false, 'reason' => 'missing reference_no'], 200);
        }

        $withdraw = Withdraw::where('iris_payout_reference_no', $refNo)->first();
        if (!$withdraw) {
            return response()->json(['ok' => false, 'reason' => 'withdraw tidak ditemukan'], 200);
        }

        if (in_array($withdraw->status, ['success', 'failed', 'rejected'])) {
            return response()->json(['ok' => true, 'note' => 'final status'], 200);
        }

        $finalStatus = match ($status) {
            'completed', 'success' => 'success',
            'failed', 'rejected'   => 'failed',
            'processed'            => 'processing',
            'queued'               => 'processing',
            default                => 'processing',
        };

        DB::transaction(function () use ($withdraw, $finalStatus, $request) {
            $withdraw->update([
                'status'        => $finalStatus,
                'processed_at'  => in_array($finalStatus, ['success', 'failed']) ? now() : null,
                'iris_response' => $request->all(),
                'failure_reason'=> $finalStatus === 'failed' ? ($request->input('error_message') ?? 'IRIS failure') : null,
            ]);

            // Refund saldo bila gagal
            if ($finalStatus === 'failed') {
                $wallet = Wallet::where('dev_id', $withdraw->dev_id)->first();
                if ($wallet) {
                    $wallet->credit((int) $withdraw->amount, "Refund: withdraw #{$withdraw->id} gagal", null);
                }
            }
        });

        Log::info("IRIS payout {$refNo} → {$finalStatus}");
        return response()->json(['ok' => true], 200);
    }
}
