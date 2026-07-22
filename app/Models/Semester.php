<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['tahun_akademik_id', 'semester', 'start_date', 'end_date', 'is_active', 'admin_id'])]
class Semester extends Model
{
    use HasFactory, SoftDeletes;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected $table = 'semesters';

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id')->withTrashed();
    }

    public function materis()
    {
        return $this->hasMany(LearningModuleMateri::class);
    }

    public function tugas()
    {
        return $this->hasMany(LearningModuleTugas::class);
    }

    public function quizzes()
    {
        return $this->hasMany(LearningModuleQuiz::class);
    }

    public function ujians()
    {
        return $this->hasMany(LearningModuleUjian::class);
    }

    public function absensis()
    {
        return $this->hasMany(LearningModuleAbsensi::class);
    }

    protected static function booted()
    {


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

    }
}
