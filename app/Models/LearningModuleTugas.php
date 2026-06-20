<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['learning_module_id', 'semester_id', 'title', 'instructions', 'due_date', 'file_path'])]
class LearningModuleTugas extends Model
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

    public function submissions()
    {
        return $this->hasMany(TugasSubmission::class, 'learning_module_tugas_id');
    }
}
