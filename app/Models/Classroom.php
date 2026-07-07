<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama_kelas', 'jurusan_id', 'tahun_akademik_id', 'is_active', 'admin_id'])]
class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class)->withTrashed();
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id')->withTrashed();
    }

    public function murids()
    {
        return $this->hasMany(Murid::class);
    }

    protected static function booted()
    {
        static::creating(function ($classroom) {
            if ($classroom->tahun_akademik_id) {
                $exists = TahunAkademik::where('id', $classroom->tahun_akademik_id)->exists();
                if (!$exists) {
                    $sem = Semester::find($classroom->tahun_akademik_id);
                    if ($sem) {
                        $classroom->tahun_akademik_id = $sem->tahun_akademik_id;
                    }
                }
            }
        });

        static::updating(function ($classroom) {
            if ($classroom->isDirty('tahun_akademik_id') && $classroom->tahun_akademik_id) {
                $exists = TahunAkademik::where('id', $classroom->tahun_akademik_id)->exists();
                if (!$exists) {
                    $sem = Semester::find($classroom->tahun_akademik_id);
                    if ($sem) {
                        $classroom->tahun_akademik_id = $sem->tahun_akademik_id;
                    }
                }
            }
        });
    }
}
