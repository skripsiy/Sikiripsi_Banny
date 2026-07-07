<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['learning_module_id', 'semester_id', 'title', 'content', 'file_path'])]
class LearningModuleMateri extends Model
{
    use HasFactory, SoftDeletes;

    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class)->withTrashed();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class)->withTrashed();
    }
}
