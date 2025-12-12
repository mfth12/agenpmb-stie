<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
  /**
   * Session WhatsApp Gateway
   * Base URL API WhatsApp Gateway
   */
  private $session;
  private $baseUrl;

  /**
   * Inisialisasi konfigurasi dari konfigs() lalu .env
   */
  private function init()
  {
    // hanya inisialisasi sekali agar lebih efisien
    if (!$this->baseUrl || !$this->session) {
      $this->baseUrl = rtrim(konfigs('WA_ENDPOINT', 'https://localhost', '/'));
      $this->session = konfigs('WA_SESSION', 'sesiwhatsapp',);
    }
  }

  /**
   * Normalisasi nomor HP ke format internasional (62xxxx)
   *
   * @param string $number
   * @return string
   */
  private function normalizeNumber(string $number): string
  {
    $number = trim($number);

    if ($number === '') {
      return '';
    }

    $number = preg_replace('/[^0-9]/', '', $number); // hilangkan karakter non-digit

    if (str_starts_with($number, '62')) {
      // nomor sudah benar, tetap diam
      return $number;
    }

    if (str_starts_with($number, '0')) {
      // ubah 08xxxx -> 628xxxx
      return '62' . substr($number, 1);
    }

    if (str_starts_with($number, '8')) {
      // ubah 8xxxx -> 628xxxx
      return '62' . $number;
    }

    // fallback (misal nomor tidak valid)
    return $number;
  }

  /**
   * Kirim pesan teks tunggal
   *
   * @param string $to   Nomor tujuan (boleh 08xxxx atau 62xxxx)
   * @param string $text Pesan yang ingin dikirim
   * @return array|null
   */
  public function sendMessage(string $to, string $text): ?array
  {
    $this->init();

    $to = $this->normalizeNumber($to);

    try {
      $response = Http::timeout(30)->post($this->baseUrl . '/send-message', [
        'session' => $this->session,
        'to'      => $to,
        'text'    => $text,
      ]);

      return $response->json();
    } catch (Throwable $e) {
      // jika ada error koneksi / timeout / gagal decode JSON
      return [
        'status'  => false,
        'error'   => $e->getMessage(),
        'to'      => $to,
      ];
    }
  }


  /**
   * Kirim pesan bulk
   *
   * @param array $data Array berisi ['to' => '08xxxx/62xxxx', 'text' => 'pesan']
   * @param int   $delay Delay per pesan (ms), default 5000
   * @return array|null
   */
  public function sendBulk(array $data, int $delay = 5000): ?array
  {
    $this->init();

    // normalisasi semua nomor di bulk
    $data = array_map(function ($item) {
      $item['to'] = $this->normalizeNumber($item['to']);
      return $item;
    }, $data);

    try {
      $response = Http::timeout(30)->post($this->baseUrl . '/send-bulk-message', [
        'session' => $this->session,
        'data'    => $data,
        'delay'   => $delay,
      ]);

      return $response->json();
    } catch (Throwable $e) {
      return [
        'status' => false,
        'error'  => $e->getMessage(),
      ];
    }
  }
}
