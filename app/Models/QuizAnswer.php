<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['quiz_attempt_id', 'bank_soal_id', 'jawaban_pg', 'jawaban_essay', 'is_correct', 'skor_manual'])]
class QuizAnswer extends Model
{
    use HasFactory;

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class)->withTrashed();
    }
}
