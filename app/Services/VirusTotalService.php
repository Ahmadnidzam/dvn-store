<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Wrapper VirusTotal API v3.
 * Dipakai Job ScanUploadedFileJob untuk memindai file upload developer.
 *
 * Docs: https://docs.virustotal.com/reference/files-scan
 *
 * Kuota free: 4 request/menit, 500/hari.
 */
class VirusTotalService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected bool $enabled;

    public function __construct()
    {
        $this->baseUrl = (string) config('dvnstore.virustotal.base_url');
        $this->apiKey  = config('dvnstore.virustotal.api_key');
        $this->enabled = (bool) config('dvnstore.virustotal.enabled');
    }

    public function isAvailable(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Upload file ke VirusTotal → return analysis id.
     * File ≤ 32 MB pakai endpoint /files. Lebih besar perlu URL khusus.
     */
    public function uploadFile(string $absolutePath): array
    {
        if (!$this->isAvailable()) {
            return [
                'ok'   => false,
                'id'   => null,
                'raw'  => ['error' => 'VirusTotal not configured. Set VIRUSTOTAL_API_KEY in .env or set VIRUSTOTAL_ENABLED=false.'],
            ];
        }

        $size = @filesize($absolutePath);
        if ($size === false) {
            return ['ok' => false, 'id' => null, 'raw' => ['error' => 'File not readable: ' . $absolutePath]];
        }

        // > 32 MB butuh upload URL khusus
        if ($size > 32 * 1024 * 1024) {
            $urlData = $this->getLargeUploadUrl();
            if (empty($urlData['ok'])) {
                return $urlData;
            }
            $uploadUrl = $urlData['url'];
        } else {
            $uploadUrl = "{$this->baseUrl}/files";
        }

        $response = Http::withHeaders(['x-apikey' => $this->apiKey])
            ->attach('file', fopen($absolutePath, 'r'), basename($absolutePath))
            ->post($uploadUrl);

        return $this->parseUploadResponse($response);
    }

    /**
     * Cek hasil analisis berdasar analysis id.
     */
    public function getAnalysis(string $analysisId): array
    {
        $response = Http::withHeaders(['x-apikey' => $this->apiKey])
            ->acceptJson()
            ->get("{$this->baseUrl}/analyses/{$analysisId}");

        $data = $response->json() ?? [];
        $stats   = $data['data']['attributes']['stats'] ?? [];
        $status  = $data['data']['attributes']['status'] ?? null;
        $malicious  = (int) ($stats['malicious'] ?? 0);
        $suspicious = (int) ($stats['suspicious'] ?? 0);

        $verdict = match (true) {
            $status !== 'completed'                  => 'scanning',
            $malicious + $suspicious > 0             => 'infected',
            default                                  => 'clean',
        };

        return [
            'ok'      => $response->successful(),
            'status'  => $status,
            'verdict' => $verdict,
            'stats'   => $stats,
            'raw'     => $data,
        ];
    }

    protected function getLargeUploadUrl(): array
    {
        $response = Http::withHeaders(['x-apikey' => $this->apiKey])
            ->acceptJson()
            ->get("{$this->baseUrl}/files/upload_url");

        $url = $response->json('data');
        return $url
            ? ['ok' => true, 'url' => $url]
            : ['ok' => false, 'id' => null, 'raw' => $response->json() ?? []];
    }

    protected function parseUploadResponse(Response $response): array
    {
        $data = $response->json() ?? [];
        $id   = $data['data']['id'] ?? null;

        return [
            'ok'  => $response->successful() && $id !== null,
            'id'  => $id,
            'raw' => $data,
        ];
    }
}
