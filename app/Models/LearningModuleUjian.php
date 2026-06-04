<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['learning_module_id', 'title', 'instructions', 'duration_minutes', 'due_date'])]
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
}
