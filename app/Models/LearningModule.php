<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['guru_id', 'mata_pelajaran_id', 'tahun_akademik_id', 'title', 'description'])]
class LearningModule extends Model
{
    use HasFactory, SoftDeletes;

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id')->withTrashed();
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
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
}
