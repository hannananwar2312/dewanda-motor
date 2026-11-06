<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function generateInsight(array $data, string $mode = 'admin'): array
    {
        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            return ['ok' => false, 'error' => 'API key Gemini belum diatur di .env', 'text' => null];
        }

        // Daftar model yang dicoba berurutan — kalau yang pertama sibuk (503),
        // otomatis lanjut ke model berikutnya.
        $models = array_values(array_unique([
            config('services.gemini.model', 'gemini-flash-lite-latest'),
            'gemini-flash-latest',
        ]));

        $prompt = $this->buildPrompt($data, $mode);

        $payload = [
            'contents' => [[
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 1024,
            ],
        ];

        $lastError = 'Gagal memanggil AI.';

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            for ($attempt = 1; $attempt <= 3; $attempt++) {
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
                        break; // coba model berikutnya
                    }

                    $status = $response->status();
                    Log::warning('Gemini API error', ['model' => $model, 'status' => $status, 'body' => $response->body()]);

                    // Sibuk / error sementara → coba lagi model yang sama
                    if (in_array($status, [429, 500, 503])) {
                        $lastError = 'Server AI sedang sibuk (status ' . $status . ')';
                        sleep($attempt); // backoff 1s, 2s, 3s
                        continue;
                    }

                    // Error lain (mis. 404 model tidak ada) → langsung pindah model
                    $lastError = 'Gagal menghubungi AI (status ' . $status . ')';
                    break;
                } catch (\Throwable $e) {
                    Log::error('Gemini exception', ['model' => $model, 'msg' => $e->getMessage()]);
                    $lastError = 'Koneksi ke AI gagal (timeout)';
                    sleep($attempt);
                }
            }
        }

        return ['ok' => false, 'error' => $lastError . ' — model Google lagi penuh/tidak stabil, coba klik Generate lagi beberapa saat.', 'text' => null];
    }

    private function buildPrompt(array $d, string $mode): string
    {
        if ($mode === 'kasir') {
            return $this->kasirPrompt($d);
        }

        return $this->adminPrompt($d);
    }

    // ===== Prompt untuk ADMIN: analisis bisnis penuh =====
    private function adminPrompt(array $d): string
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

    // ===== Prompt untuk KASIR: bantuan jualan & promo saja =====
    private function kasirPrompt(array $d): string
    {
        // Hanya data yang relevan untuk melayani pembeli — tanpa omzet/modal/nilai stok.
        $ringkas = [
            'produk_terlaris'      => $d['produk_terlaris'] ?? [],
            'stok_menipis'         => $d['stok_menipis'] ?? [],
            'produk_belum_terjual' => $d['produk_belum_terjual'] ?? [],
        ];
        $json = json_encode($ringkas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Kamu adalah asisten penjualan yang membantu KASIR di toko sparepart motor "Dewanda Motor".
Berikut data produk toko dalam format JSON (hanya info produk, bukan data keuangan):

$json

Tugasmu: beri tips praktis supaya kasir bisa jualan lebih banyak saat melayani pembeli.

Aturan menjawab:
- Gunakan Bahasa Indonesia yang santai, singkat, dan ramah seperti ngobrol ke rekan kerja.
- Mulai dengan 1 kalimat semangat pembuka.
- Lalu beri maksimal 5 poin tips, tiap poin diawali tanda "- ".
- Fokus HANYA pada: produk laris yang layak ditawarkan/di-upsell ke pembeli, ide bundling/paket (misal beli oli sekalian filter), dan produk yang belum laku supaya ditawarkan/dipromokan ke pembeli.
- JANGAN membahas omzet, modal, keuntungan, nilai stok, atau urusan restok/pengadaan barang — itu urusan admin, bukan kasir.
- Jangan pakai angka rupiah atau nominal uang.
- JANGAN mengarang produk di luar yang diberikan di atas.
PROMPT;
    }
}