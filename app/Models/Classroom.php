<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama_kelas', 'jurusan_id', 'tahun_ajaran_id', 'is_active'])]
class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(AcademicYear::class, 'tahun_ajaran_id');
    }

    public function murids()
    {
        return $this->hasMany(Murid::class);
    }

    protected static function booted()
    {
        static::creating(function ($classroom) {
            if ($classroom->tahun_ajaran_id) {
                $exists = AcademicYear::where('id', $classroom->tahun_ajaran_id)->exists();
                if (!$exists) {
                    $ta = TahunAjaran::find($classroom->tahun_ajaran_id);
                    if ($ta) {
                        $classroom->tahun_ajaran_id = $ta->academic_year_id;
                    }
                }
            }
        });

        static::updating(function ($classroom) {
            if ($classroom->isDirty('tahun_ajaran_id') && $classroom->tahun_ajaran_id) {
                $exists = AcademicYear::where('id', $classroom->tahun_ajaran_id)->exists();
                if (!$exists) {
                    $ta = TahunAjaran::find($classroom->tahun_ajaran_id);
                    if ($ta) {
                        $classroom->tahun_ajaran_id = $ta->academic_year_id;
                    }
                }
            }
        });
    }
}
