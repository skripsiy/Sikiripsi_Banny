<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['subject_id', 'guru_id', 'tipe', 'pertanyaan', 'gambar_path', 'pembahasan'])]
class BankSoal extends Model
{
    use HasFactory, SoftDeletes;

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function options()
    {
        return $this->hasMany(BankSoalOption::class);
    }
}
