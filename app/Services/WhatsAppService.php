<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Log aman: tidak akan pernah melempar exception ke request
     */
    private static function log($level, $message)
    {
        try {
            Log::$level($message);
        } catch (\Throwable $e) {
            // abaikan kegagalan logging
        }
    }

    public static function normalizeNumber($noHp)
    {
        $noHp = preg_replace('/[^0-9]/', '', (string) $noHp);

        if (str_starts_with($noHp, '62')) return $noHp;
        if (str_starts_with($noHp, '0'))  return '62' . substr($noHp, 1);
        if (str_starts_with($noHp, '8'))  return '62' . $noHp;
        return $noHp;
    }

    public static function sendNotification($noHp, $namaSiswa, $status, $tanggal, $keterangan)
    {
        $noHp = self::normalizeNumber($noHp);

        $statusText = match($status) {
            'sakit' => 'SAKIT',
            'izin' => 'IZIN',
            'alpha' => 'TIDAK HADIR TANPA KETERANGAN (ALPHA)',
            'bolos' => 'BOLOS',
            'terlambat' => 'TERLAMBAT',
            default => strtoupper($status)
        };

        try {
            $tanggalFormat = \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');
        } catch (\Throwable $e) {
            $tanggalFormat = $tanggal;
        }

        $ketText = $keterangan ? "\nKeterangan: {$keterangan}" : "";

        $pesan = "Yth. Wali Murid dari *{$namaSiswa}*,\n\n" .
                 "Kami informasikan bahwa pada hari *{$tanggalFormat}*, anak Anda tercatat:\n" .
                 "*STATUS: {$statusText}*{$ketText}\n\n" .
                 "Mohon perhatian dan kerjasamanya. Terima kasih.\n" .
                 "_- Sistem Informasi Jurnal Kelas_";

        return self::kirim($noHp, $pesan, "Notif {$namaSiswa} ({$status})");
    }

    public static function sendPesanManual($noHp, $pesan)
    {
        return self::kirim(self::normalizeNumber($noHp), $pesan, 'Pesan manual');
    }

    /**
     * Inti pengiriman: timeout pendek + tahan terhadap semua kegagalan
     */
    private static function kirim($noHp, $pesan, $label)
    {
        $apiUrl = env('WA_API_URL', 'https://api.fonnte.com/send');
        $apiToken = env('WA_API_TOKEN');

        if (empty($apiToken)) {
            self::log('error', '[WA] WA_API_TOKEN kosong!');
            return false;
        }

        try {
            self::log('info', "[WA] Mengirim {$label} ke {$noHp}");

            $response = Http::timeout(10)->connectTimeout(5)->withHeaders([
                'Authorization' => $apiToken,
            ])->asForm()->post($apiUrl, [
                'target' => $noHp,
                'message' => $pesan,
            ]);

            $body = $response->json() ?? [];
            $ok = $response->successful() && ($body['status'] ?? false);

            self::log($ok ? 'info' : 'error', '[WA] ' . ($ok ? 'BERHASIL' : 'GAGAL') . " ke {$noHp}: " . json_encode($body));
            return $ok;
        } catch (\Throwable $e) {
            self::log('error', '[WA] Exception: ' . $e->getMessage());
            return false;   // ⚠️ Aplikasi TIDAK akan crash walau WA gagal
        }
    }
}