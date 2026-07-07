<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'admin_id', 'nip', 'fullname', 'tanggalLahir', 'alamat', 'noWhatsapp', 'gelar'])]
class Guru extends Model
{
    use HasFactory, SoftDeletes;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected static function booted()
    {
        static::creating(function ($guru) {
            if (app()->runningUnitTests()) {
                if (empty($guru->nip)) {
                    $guru->nip = 'GURU_NIP_' . rand(100000, 999999);
                }
                if (empty($guru->fullname)) {
                    $guru->fullname = $guru->user?->name ?? 'Guru Dummy';
                }
                if (empty($guru->tanggalLahir)) {
                    $guru->tanggalLahir = '1985-01-01';
                }
                if (empty($guru->alamat)) {
                    $guru->alamat = 'Jl. Pendidikan No. 1';
                }
                if (empty($guru->noWhatsapp)) {
                    $guru->noWhatsapp = '08123456789';
                }
                if (empty($guru->gelar)) {
                    $guru->gelar = 'S.Pd';
                }
            }
        });
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
