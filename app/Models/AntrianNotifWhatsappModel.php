<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntrianNotifWhatsappModel extends Model
{
    protected $table = 'antrian_whatsapps';
    protected $primaryKey = 'antrian_id';

    protected $fillable = [
        'user_id',
        'sesi',
        'target',
        'tipe',
        'isi_pesan',
        'status',
        'retry_count', // Tambahkan ini jika ingin di-fill juga
    ];

    protected $casts = [
        'status' => 'integer',
        'retry_count' => 'integer', // Tambahkan cast untuk retry_count
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }

    public const PENDING    = 0; // pending (akan terus dicoba).
    public const SUKSES     = 1; // sukses, alhamdulillah
    public const GAGAL      = 2; // gagal sekali, tapi kita langsung reset ke 0 pending supaya ikut diproses lagi.
    public const DEAD       = 3; // dead letter (sudah lewat batas retry, perlu dicek manual).
}
