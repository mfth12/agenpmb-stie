<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAfiliasi extends Model
{
    use HasFactory;

    protected $table = 'user_afiliasis';
    protected $primaryKey = 'afiliasi_id';
    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'nama',
        'keterangan',
    ];

    /**
     * Relasi ke parent (afiliasi induk)
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relasi ke children (afiliasi anak)
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Scope untuk mengambil hanya afiliasi root (parent_id = null)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope untuk mengambil children dari suatu parent
     */
    public function scopeChildrenOf($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }
}
