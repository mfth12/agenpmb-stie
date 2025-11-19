<?php

namespace App\Models;

use App\Models\UserAfiliasi;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    /**
     * @use HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    protected $primaryKey     = 'user_id';
    protected $guarded        = ['user_id'];
    protected static $logName = 'sistem';
    public $timestamps        = true;

    /**
     * key name route pada model ini.
     *
     * @var string
     */
    public function getRouteKeyName()
    {
        return 'user_id';
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * untuk casting datetime
     *
     */
    protected $casts = [
        'last_logged_in' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'siakad_id',
        'username',
        'name',
        'email',
        'nomor_hp',
        'nomor_hp2',
        'asal_sekolah',
        'afiliasi', // Tambahan
        'email_verified_at',
        'about',
        'default_role',
        'theme',
        'avatar',
        'status',
        'status_login',
        'isdeleted',
        'last_logged_in',
        'last_synced_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel user_afiliasis
     * Seorang user bisa memiliki satu afiliasi
     */
    public function afiliasi()
    {
        return $this->belongsTo(UserAfiliasi::class, 'afiliasi', 'afiliasi_id');
    }

    /**
     * Accessor untuk status_badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge badge-pill px-2 bg-success text-success-fg">Aktif</span>',
            'inactive' => '<span class="badge badge-pill px-2 bg-danger text-danger-fg">Tidak Aktif</span>',
            'pending' => '<span class="badge badge-pill px-2 bg-warning text-warning-fg">Pending</span>',
            default => '<span class="badge badge-pill badge-outline px-2 bg-light text-light-fg">Tidak Diketahui</span>',
        };
    }

    /**
     * Register media collections and conversions
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10)
                    ->optimize();

                $this->addMediaConversion('medium')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10)
                    ->optimize();

                $this->addMediaConversion('large')
                    ->width(800)
                    ->height(800)
                    ->sharpen(10)
                    ->optimize();
            });
    }

    /**
     * Custom method untuk upload avatar dengan kompresi dan nama random
     */
    public function uploadAvatar($file)
    {
        // Hapus avatar lama jika ada
        $this->clearMediaCollection('avatar');

        // Generate nama file random
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . time() . '.' . $extension;

        // Upload file baru dengan nama random
        return $this->addMedia($file)
            ->usingFileName($fileName) // Gunakan nama file random
            ->withCustomProperties([
                'original_name' => $file->getClientOriginalName(), // Simpan nama asli di custom properties
                'original_size' => $file->getSize(),
                'uploaded_at' => now()->toDateTimeString(),
            ])
            ->toMediaCollection('avatar');
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar') ?: ($this->avatar ? env('URL_ASSET_SIAKAD') . '/' . $this->avatar : asset('img/default.png'));
    }

    /**
     * Get avatar thumbnail URL
     */
    public function getAvatarThumbUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar', 'thumb') ?: ($this->avatar ? env('URL_ASSET_SIAKAD') . '/' . $this->avatar : asset('img/default.png'));
    }

    /**
     * Get avatar medium URL
     */
    public function getAvatarMediumUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar', 'medium') ?: ($this->avatar ? env('URL_ASSET_SIAKAD') . '/' . $this->avatar : asset('img/default.png'));
    }

    /**
     * Check if user has custom avatar
     */
    public function getHasCustomAvatarAttribute()
    {
        return $this->getFirstMedia('avatar') !== null;
    }

    /**
     * Additional according to siakad
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if (function_exists('proc_open')) {
            $this->addMediaConversion('thumb')
                ->width(350)
                ->height(350);
        } else {
            $this->addMediaConversion('thumb')
                ->width(350)
                ->height(350)
                ->quality(70);
        }
    }

    /**
     * Additional according to siakad
     * digunakan untuk mengambil nama tabel model ini.
     *
     * @return string
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
