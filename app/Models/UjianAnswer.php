<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ujian_attempt_id', 'bank_soal_id', 'jawaban_pg', 'jawaban_essay', 'is_correct', 'skor_manual'])]
class UjianAnswer extends Model
{
    use HasFactory;

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(UjianAttempt::class, 'ujian_attempt_id');
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class)->withTrashed();
    }
}
