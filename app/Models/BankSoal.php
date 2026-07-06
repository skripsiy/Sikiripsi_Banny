<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['mata_pelajaran_id', 'guru_id', 'tipe', 'pertanyaan', 'gambar_path', 'pembahasan'])]
class BankSoal extends Model
{
    use HasFactory, SoftDeletes;

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id')->withTrashed();
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
