<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['tahun_ajaran', 'semester', 'is_active'])]
class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($tahunAjaran) {
            if ($tahunAjaran->is_active) {
                $query = static::where('is_active', true);
                if ($tahunAjaran->id) {
                    $query->where('id', '!=', $tahunAjaran->id);
                }
                $query->update(['is_active' => false]);
            }
        });
    }
}
