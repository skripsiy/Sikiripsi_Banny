<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['learning_module_id', 'semester_id', 'title', 'instructions', 'duration_minutes', 'due_date'])]
class LearningModuleUjian extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function soals()
    {
        return $this->belongsToMany(BankSoal::class, 'ujian_soals', 'learning_module_ujian_id', 'bank_soal_id')
            ->withPivot('id', 'urutan', 'bobot')
            ->orderByPivot('urutan')
            ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(UjianAttempt::class, 'learning_module_ujian_id');
    }
}
