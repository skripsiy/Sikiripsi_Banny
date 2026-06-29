<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'nuptk', 'admin_id', 'nip', 'fullname', 'tanggalLahir', 'alamat', 'noWhatsapp', 'gelar'])]
class Guru extends Model
{
    use HasFactory, SoftDeletes;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mataPelajarans()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajaran', 'guru_id', 'mata_pelajaran_id')->withTimestamps();
    }

    public function bankSoals()
    {
        return $this->hasMany(BankSoal::class);
    }
}
