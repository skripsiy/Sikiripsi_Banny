<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['tahun_akademik_id', 'tahun_ajaran', 'semester', 'is_active'])]
class Semester extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'semesters';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    protected static function booted()
    {
        static::creating(function ($semester) {
            if (!$semester->tahun_akademik_id && $semester->tahun_ajaran) {
                $ay = TahunAkademik::withTrashed()->where('tahun_ajaran', $semester->tahun_ajaran)->first();
                if ($ay) {
                    if ($ay->trashed()) {
                        $ay->restore();
                    }
                } else {
                    $ay = TahunAkademik::create([
                        'tahun_ajaran' => $semester->tahun_ajaran,
                        'is_active' => $semester->is_active,
                    ]);
                }
                $semester->tahun_akademik_id = $ay->id;
            }
        });

        static::saving(function ($semester) {
            if ($semester->is_active) {
                if ($semester->tahun_akademik_id) {
                    TahunAkademik::where('id', $semester->tahun_akademik_id)
                        ->update(['is_active' => true]);
                }
            } else {
                if ($semester->tahun_akademik_id) {
                    $hasActive = static::where('tahun_akademik_id', $semester->tahun_akademik_id)
                        ->where('id', '!=', $semester->id)
                        ->where('is_active', true)
                        ->exists();
                    if (!$hasActive) {
                        TahunAkademik::where('id', $semester->tahun_akademik_id)
                            ->update(['is_active' => false]);
                    }
                }
            }
        });

        static::deleted(function ($semester) {
            $ay = $semester->tahunAkademik;
            if ($ay && $ay->semesters()->count() === 0) {
                $ay->delete();
            }
        });
    }
}
