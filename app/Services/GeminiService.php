<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function generateInsight(array $data): array
    {
        $apiKey = config('services.gemini.key');
        $model  = config('services.gemini.model', 'gemini-2.0-flash');

        if (empty($apiKey)) {
            return ['ok' => false, 'error' => 'API key Gemini belum diatur di .env', 'text' => null];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [[
                'parts' => [['text' => $this->buildPrompt($data)]],
            ]],
            'generationConfig' => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 2048,
            ],
        ];

        $lastError = 'Gagal memanggil AI.';

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            try {
                $response = Http::timeout(20)
                    ->connectTimeout(8)
                    ->withOptions([
                        'force_ip_resolve' => 'v4',
                        'version' => '1.1',
                    ])
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

                    if ($text) {
                        return ['ok' => true, 'error' => null, 'text' => trim($text)];
                    }

                    $lastError = 'AI tidak mengembalikan jawaban';
                    break;
                }

                $status = $response->status();
                Log::warning('Gemini API error', ['status' => $status, 'body' => $response->body()]);

                if (in_array($status, [429, 500, 503])) {
                    $lastError = 'Server AI sedang sibuk (status ' . $status . ')';
                    sleep(1);
                    continue;
                }

                return ['ok' => false, 'error' => 'Gagal menghubungi AI (status ' . $status . ')', 'text' => null];
            } catch (\Throwable $e) {
                Log::error('Gemini exception', ['msg' => $e->getMessage()]);
                $lastError = 'Koneksi ke AI gagal (timeout)';
                sleep(1);
            }
        }

        return ['ok' => false, 'error' => $lastError . ' — koneksi/serlver Google lagi tidak stabil, klik Generate lagi.', 'text' => null];
    }

    private function buildPrompt(array $d): string
    {
        $json = json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Kamu adalah analis bisnis untuk toko sparepart motor bernama "Dewanda Motor".
Berikut ringkasan data penjualan & stok toko dalam format JSON:

$json

Tugasmu: berikan analisis singkat dan REKOMENDASI yang bisa langsung ditindaklanjuti pemilik toko.

Aturan menjawab:
- Gunakan Bahasa Indonesia yang santai dan mudah dipahami.
- Mulai dengan 1 kalimat ringkasan kondisi toko.
- Lalu beri maksimal 5 poin rekomendasi, tiap poin diawali tanda "- ".
- Fokus: produk yang harus di-restok, produk yang perlu didorong penjualannya, dan stok mati (belum laku) yang perlu diobral.
- Angka uang pakai format rupiah.
- JANGAN mengarang data di luar yang diberikan di atas.
PROMPT;
    }
}