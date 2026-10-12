<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function sendNotification($noHp, $namaSiswa, $status, $tanggal, $keterangan)
    {
        // Format nomor HP: ganti 08xx menjadi 628xx
        $noHp = preg_replace('/^08/', '628', $noHp);

        // Mapping status ke bahasa yang lebih sopan untuk orang tua
        $statusText = match($status) {
            'sakit' => 'SAKIT',
            'izin' => 'IZIN',
            'alpha' => 'TIDAK HADIR TANPA KETERANGAN (ALPHA)',
            'bolos' => 'BOLOS',
            'terlambat' => 'TERLAMBAT',
            default => strtoupper($status)
        };

        $tanggalFormat = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
        $ketText = $keterangan ? "\nKeterangan: {$keterangan}" : "";

        $pesan = "Yth. Wali Murid dari *{$namaSiswa}*,\n\n" .
                 "Kami informasikan bahwa pada hari *{$tanggalFormat}*, anak Anda tercatat:\n" .
                 "*STATUS: {$statusText}*{$ketText}\n\n" .
                 "Mohon perhatian dan kerjasamanya. Terima kasih.\n" .
                 "_- Sistem Informasi Jurnal Kelas_";

        // --- KONFIGURASI API WHATSAPP GATEWAY (Contoh: Fonnte) ---
        // Ganti URL dan Header sesuai dokumentasi provider yang Anda gunakan
        $apiUrl = env('WA_API_URL', 'https://api.fonnte.com/send');
        $apiToken = env('WA_API_TOKEN', 'YOUR_FONNTE_TOKEN');

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->asForm()->post($apiUrl, [
                'target' => $noHp,
                'message' => $pesan,
                'countryCode' => '62', // Opsional, tergantung provider
            ]);

            if ($response->successful()) {
                Log::info("WA Terkirim ke {$noHp}: {$namaSiswa} - {$status}");
                return true;
            } else {
                Log::error("Gagal kirim WA ke {$noHp}. Response: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error koneksi WA API: " . $e->getMessage());
            return false;
        }
    }
}