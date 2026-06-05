<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['academic_year_id', 'tahun_ajaran', 'semester', 'is_active'])]
class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    protected static function booted()
    {
        static::creating(function ($tahunAjaran) {
            if (!$tahunAjaran->academic_year_id && $tahunAjaran->tahun_ajaran) {
                $ay = AcademicYear::withTrashed()->where('tahun_ajaran', $tahunAjaran->tahun_ajaran)->first();
                if ($ay) {
                    if ($ay->trashed()) {
                        $ay->restore();
                    }
                } else {
                    $ay = AcademicYear::create([
                        'tahun_ajaran' => $tahunAjaran->tahun_ajaran,
                        'is_active' => $tahunAjaran->is_active,
                    ]);
                }
                $tahunAjaran->academic_year_id = $ay->id;
            }
        });

        static::saving(function ($tahunAjaran) {
            if ($tahunAjaran->is_active) {
                if ($tahunAjaran->academic_year_id) {
                    AcademicYear::where('id', $tahunAjaran->academic_year_id)
                        ->update(['is_active' => true]);
                }
            } else {
                if ($tahunAjaran->academic_year_id) {
                    $hasActive = static::where('academic_year_id', $tahunAjaran->academic_year_id)
                        ->where('id', '!=', $tahunAjaran->id)
                        ->where('is_active', true)
                        ->exists();
                    if (!$hasActive) {
                        AcademicYear::where('id', $tahunAjaran->academic_year_id)
                            ->update(['is_active' => false]);
                    }
                }
            }
        });

        static::deleted(function ($tahunAjaran) {
            $ay = $tahunAjaran->academicYear;
            if ($ay && $ay->tahunAjarans()->count() === 0) {
                $ay->delete();
            }
        });
    }
}
