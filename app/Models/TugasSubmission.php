<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'learning_module_tugas_id',
    'murid_id',
    'file_path',
    'catatan_murid',
    'nilai',
    'catatan_guru',
    'submitted_at',
    'graded_at'
])]
class TugasSubmission extends Model
{
    use HasFactory;

    protected $table = 'learning_module_tugas_submissions';

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function tugas()
    {
        return $this->belongsTo(LearningModuleTugas::class, 'learning_module_tugas_id')->withTrashed();
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class)->withTrashed();
    }
}
