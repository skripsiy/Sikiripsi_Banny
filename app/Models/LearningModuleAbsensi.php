<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['learning_module_id', 'murid_id', 'semester_id', 'date', 'status'])]
class LearningModuleAbsensi extends Model
{
    use HasFactory;

    protected $table = 'learning_module_absensis';

    protected $casts = [
        'date' => 'date',
    ];

    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
}
