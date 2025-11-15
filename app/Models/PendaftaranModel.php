<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranModel extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';
    protected $primaryKey = 'pendaftaran_id';

    protected $fillable = [
        'user_id',
        'agen_id',
        'id_calon_mahasiswa',
        'username_siakad',
        'password_text', // penambahan plain pswd
        'no_transaksi',
        'prodi_id',
        'prodi_nama',
        'tahun',
        'gelombang',
        'biaya',
        'kelas',
        'nama_lengkap',
        'email',
        'nomor_hp',
        'nomor_hp2',
        'status',
        'keterangan',
        'response_data',
        'synced_at',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'tahun' => 'integer',
        'gelombang' => 'integer',
        'synced_at' => 'datetime',
        'response_data' => 'array',
    ];

    /**
     * Relasi ke user (calon mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke agen
     */
    public function agen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agen_id', 'user_id');
    }

    /**
     * Scope untuk pendaftaran berhasil
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope untuk pendaftaran pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk pendaftaran gagal
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // --- SCOPE BARU ---
    /**
     * Scope untuk pendaftaran yang telah disinkronkan
     */
    public function scopeSynced($query)
    {
        return $query->where('status', 'synced');
    }

    /**
     * Scope untuk pendaftaran yang diimpor
     */
    public function scopeImported($query)
    {
        return $query->where('status', 'imported');
    }

    /**
     * Scope untuk pendaftaran yang dihapus (jika statusnya 'removed')
     * Atau jika 'removed' berarti soft deleted, gunakan scopeTrashed
     */
    public function scopeRemoved($query)
    {
        return $query->where('status', 'removed');
        // Jika 'removed' berarti soft deleted, dan kolomnya adalah 'deleted_at':
        // return $query->whereNotNull('deleted_at');
    }

    /**
     * Accessor untuk status_badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'success' => '<span class="badge badge-pill px-2 bg-success text-success-fg">Berhasil</span>',
            'failed' => '<span class="badge badge-pill px-2 bg-danger text-danger-fg">Gagal</span>',
            'synced' => '<span class="badge badge-pill px-2 bg-info text-info-fg">Tersinkron</span>',
            'imported' => '<span class="badge badge-pill badge-outline px-2 bg-light text-light-fg">Diimpor</span>',
            default => '<span class="badge badge-pill px-2 bg-warning text-warning-fg">Pending</span>',
        };
    }

    /**
     * Accessor untuk format biaya
     */
    public function getBiayaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    /**
     * Accessor untuk nama kelas
     */
    public function getNamaKelasAttribute(): string
    {
        return match ($this->kelas) {
            '0' => 'Reguler Pagi',
            // '1' => 'Reguler Sore',
            '2' => 'Malam',
            // '3' => 'International',
            '5' => 'Kemitraan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Fungsi static untuk nama kelas
     */
    public static function daftarKelas(): array
    {
        return [
            '0' => 'Reguler Pagi',
            // '1' => 'Reguler Sore',
            '2' => 'Malam',
            // '3' => 'International',
            '5' => 'Kemitraan',
        ];
    }

    /**
     * Fungsi static untuk nama kelas yang dinonaktifkan (tidak bisa dipilih)
     */
    public static function daftarKelasWithNonaktif(): array
    {
        return [
            // '1', // Reguler Sore
            '3', // International
        ];
    }

    /**
     * Fungsi static untuk daftar prodi yang tersedia saat pendaftaran
     */
    public static function daftarProdiAktif(): array
    {
        return [
            '1000' => 'Manajemen',
            '1001' => 'Akuntansi',
            // '1002' => 'D III Akuntansi', // ← Nonaktifkan sementara
        ];
    }

    /**
     * Fungsi static untuk daftar prodi yang dinonaktifkan (tidak bisa dipilih)
     */
    public static function daftarProdiWithNonaktif(): array
    {
        return [
            '1002', // D III Akuntansi
        ];
    }
}
