<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
  /**
   * Session WhatsApp Gateway (gunakan dari .env biar gampang diganti)
   */
  private $session;

  /**
   * Base URL API WhatsApp Gateway
   */
  private $baseUrl;

  /**
   * Inisialisasi konfigurasi dari .env
   */
  private function init()
  {
    $this->baseUrl = konfigs('wa.endpoint', env('WA_GATEWAY_URL', 'https://wa.stie-pembangunan.ac.id'));
    $this->session = konfigs('wa.session', env('WA_GATEWAY_SESSION', 'notifstie87x6v8r2js'));
  }

  /**
   * Normalisasi nomor HP ke format internasional (62xxxx)
   *
   * @param string $number
   * @return string
   */
  private function normalizeNumber(string $number): string
  {
    $number = preg_replace('/[^0-9]/', '', $number); // hilangkan karakter non-digit

    if (str_starts_with($number, '0')) {
      // ubah 08xxxx -> 628xxxx
      $number = '62' . substr($number, 1);
    } elseif (str_starts_with($number, '8')) {
      // ubah 8xxxx -> 628xxxx
      $number = '62' . $number;
    }

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

    $response = Http::post($this->baseUrl . '/send-message', [
      'session' => $this->session,
      'to'      => $to,
      'text'    => $text,
    ]);

    return $response->json();
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

    $response = Http::post($this->baseUrl . '/send-bulk-message', [
      'session' => $this->session,
      'data'    => $data,
      'delay'   => $delay,
    ]);

    return $response->json();
  }
}
