<?php

namespace App\Services;

use App\Models\Withdraw;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Wrapper Midtrans IRIS Disbursement API (untuk withdraw developer).
 *
 * Flow:
 *   1) Developer request withdraw → kita panggil createPayout()
 *   2) IRIS proses → callback ke /payout/iris/callback → update Withdraw status
 *
 * Docs: https://iris-docs.midtrans.com/
 */
class MidtransPayoutService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string) config('dvnstore.iris.api_key');
        $this->baseUrl = config('dvnstore.iris.is_production')
            ? config('dvnstore.iris.base_url_production')
            : config('dvnstore.iris.base_url_sandbox');
    }

    /**
     * Kirim request payout untuk 1 withdraw.
     *
     * @return array{ok: bool, reference_no: ?string, status: ?string, raw: array}
     */
    public function createPayout(Withdraw $withdraw): array
    {
        if (empty($this->apiKey)) {
            return [
                'ok'           => false,
                'reference_no' => null,
                'status'       => 'failed',
                'raw'          => ['error' => 'IRIS API key not configured.'],
            ];
        }

        $bank = $withdraw->bank_snapshot;

        $payload = [
            'payouts' => [[
                'beneficiary_name'    => $bank['bank_account_holder'] ?? '',
                'beneficiary_account' => $bank['bank_account_number'] ?? '',
                'beneficiary_bank'    => strtolower($bank['bank_name'] ?? ''),
                'amount'              => (string) $withdraw->amount,
                'notes'               => 'DVNStore withdraw #' . $withdraw->id,
            ]],
        ];

        $response = Http::withBasicAuth($this->apiKey, '')
            ->acceptJson()
            ->post("{$this->baseUrl}/payouts", $payload);

        return $this->parsePayoutResponse($response);
    }

    /**
     * (Opsional) Approve payout via API. Banyak akun IRIS men-set ini di dashboard,
     * tapi kita siapkan helper-nya.
     */
    public function approvePayout(string $referenceNo, ?string $otp = null): Response
    {
        return Http::withBasicAuth($this->apiKey, '')
            ->acceptJson()
            ->post("{$this->baseUrl}/payouts/approve", [
                'reference_nos' => [$referenceNo],
                'otp'           => $otp,
            ]);
    }

    /**
     * Cek status payout berdasarkan reference number.
     */
    public function getPayoutStatus(string $referenceNo): array
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->acceptJson()
            ->get("{$this->baseUrl}/payouts/{$referenceNo}");

        return $response->json() ?? [];
    }

    protected function parsePayoutResponse(Response $response): array
    {
        $data = $response->json() ?? [];

        if (!$response->successful() || empty($data['payouts'][0]['reference_no'])) {
            return [
                'ok'           => false,
                'reference_no' => null,
                'status'       => 'failed',
                'raw'          => $data ?: ['error' => 'IRIS HTTP ' . $response->status()],
            ];
        }

        $payout = $data['payouts'][0];
        return [
            'ok'           => true,
            'reference_no' => $payout['reference_no'],
            'status'       => $payout['status'] ?? 'queued',
            'raw'          => $data,
        ];
    }
}
