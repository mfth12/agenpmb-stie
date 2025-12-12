<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class KonfigurasiModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'konfigurasis';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'config_group',
        'config_key',
        'config_value',
        'value_1',
        'value_2',
        'value_3',
        'value_4',
        'value_5'
    ];

    /**
     * Enable auto_increment.
     *
     * @var string
     */
    public $incrementing = true;

    /**
     * activated timestamps.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Digunakan untuk  menyimpan ke cache.
     *
     */
    public static function toCache()
    {
        $konfigs = KonfigurasiModel::all()->pluck('config_value', 'config_key')->toArray();
        Cache::put('konfigs', $konfigs);
    }

    /**
     * Digunakan untuk  melakukan refresh cache.
     *
     */
    public static function refreshCache()
    {
        Cache::flush();
        Cache::forget('konfigs');
        Cache::rememberForever('konfigs', function () {
            return self::pluck('config_value', 'config_key');
        });
    }

    /**
     * Digunakan untuk mengambil nama tabel model ini.
     *
     * @return string
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
