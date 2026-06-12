<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['guru_id', 'subject_id', 'tahun_ajaran_id', 'title', 'description', 'file_path'])]
class LearningModule extends Model
{
    use HasFactory, SoftDeletes;

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(AcademicYear::class, 'tahun_ajaran_id');
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
