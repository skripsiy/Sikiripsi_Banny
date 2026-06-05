<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['bank_soal_id', 'label', 'teks_opsi', 'is_correct'])]
class BankSoalOption extends Model
{
    use HasFactory;

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }
}
