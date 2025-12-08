<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AntrianNotifWhatsappModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const LOG_CHANNEL   = 'whatsapp';
    const MAX_RETRY     = 20;   // batas retry, misal 20 kali

    public $tries       = 5;    // Laravel retry internal
    public $backoff     = 30;   // Jeda 30 detik antar percobaan

    /** 
     * @var \App\Models\AntrianNotifWhatsappModel 
     */
    public $antrian;

    /**
     * Construct function
     */
    public function __construct(AntrianNotifWhatsappModel $antrian)
    {
        $this->antrian = $antrian;
    }

    /**
     * Handle job function
     */
    public function handle(WhatsappService $wa): void
    {
        // hanya proses jika masih pending // 0
        if ($this->antrian->status !== AntrianNotifWhatsappModel::PENDING) {
            return;
        }

        // kirim ke gateway melalui bantuan service
        $res = $wa->sendMessage(
            $this->antrian->target,
            $this->antrian->isi_pesan
        );

        // mengambil status dari respon wa gateway
        $status = data_get($res, 'data.status', 0); // default 0

        if ($status === 1) {
            // sukses
            $this->antrian->status = AntrianNotifWhatsappModel::SUKSES;
        } else {
            // jika gagal, increment retry count
            $this->antrian->retry_count = ($this->antrian->retry_count ?? 0) + 1;

            // jika melebihi batas maksimal
            if ($this->antrian->retry_count >= self::MAX_RETRY) {
                $this->antrian->status = AntrianNotifWhatsappModel::DEAD;
                $this->antrian->save(); // simpan dulu ke DB

                // log khusus dead letter agar mudah dimonitor
                Log::channel(self::LOG_CHANNEL)->error('NotifWhatsappJob DEAD LETTER', [
                    'antrian_id'      => $this->antrian->antrian_id,
                    'target'          => $this->antrian->target,
                    'retry_count'     => $this->antrian->retry_count,
                    'response'        => $res,
                ]);
                // jangan buat log processed lagi
                return;

                // optional: bisa dispatch event atau notifikasi admin
                // event(new NotifWhatsappDead($this->antrian));
            } else {
                $this->antrian->status = AntrianNotifWhatsappModel::PENDING; // tetap pending
            }
        }

        $this->antrian->save();

        // logging
        Log::channel(self::LOG_CHANNEL)->info("NotifWhatsappJob untuk user_id '{$this->antrian->user_id}' dengan tujuan '{$this->antrian->target}'  berhasil diproses", [
            'antrian_id'      => $this->antrian->antrian_id,
            'target'          => $this->antrian->target,
            'res_status'      => $status,
            'retry_count'     => $this->antrian->retry_count,
            'final_db_status' => $this->antrian->status,
            'response'        => $res,
        ]);
    }
}
