<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['learning_module_ujian_id', 'murid_id', 'started_at', 'finished_at', 'skor', 'status'])]
class UjianAttempt extends Model
{
    use HasFactory;

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function ujian()
    {
        return $this->belongsTo(LearningModuleUjian::class, 'learning_module_ujian_id');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }

    public function answers()
    {
        return $this->hasMany(UjianAnswer::class);
    }

    public function isExpired()
    {
        if ($this->finished_at) {
            return false;
        }

        $duration = $this->ujian->duration_minutes;
        $expiryTime = $this->started_at->copy()->addMinutes($duration);

        return now()->isAfter($expiryTime);
    }

    public function calculateScore()
    {
        $ujian = $this->ujian;
        $soals = $ujian->soals;
        $totalWeight = 0;
        $scoredWeight = 0;

        $soalPivot = [];
        foreach ($soals as $s) {
            $soalPivot[$s->id] = $s->pivot->bobot;
            $totalWeight += $s->pivot->bobot;
        }

        $answers = $this->answers()->with('bankSoal.options')->get();

        foreach ($answers as $ans) {
            $weight = $soalPivot[$ans->bank_soal_id] ?? 1;
            if ($ans->bankSoal->tipe === 'pg') {
                $correctOption = $ans->bankSoal->options->firstWhere('is_correct', true);
                $isCorrect = $correctOption && $ans->jawaban_pg === $correctOption->label;
                $ans->is_correct = $isCorrect;
                if ($isCorrect) {
                    $scoredWeight += $weight;
                }
            } else {
                if ($ans->skor_manual !== null) {
                    $scoredWeight += $ans->skor_manual;
                }
            }
            $ans->save();
        }

        $finalScore = $totalWeight > 0 ? ($scoredWeight / $totalWeight) * 100 : 0;
        $this->skor = min(100, max(0, round($finalScore, 2)));

        $hasUnresolvedEssay = $answers->contains(function($ans) {
            return $ans->bankSoal->tipe === 'essay' && $ans->skor_manual === null;
        });

        $this->status = $hasUnresolvedEssay ? 'submitted' : 'graded';
        $this->save();
    }
}
