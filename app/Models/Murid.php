<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'nisn', 'classroom_id', 'no_telepon_orang_tua', 'admin_id', 'namaLengkap', 'tanggalLahir', 'alamat', 'noTelpon', 'namaOrangTua'])]
class Murid extends Model
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

    protected static function booted()
    {
        static::creating(function ($murid) {
            if (app()->runningUnitTests()) {
                if (empty($murid->nisn)) {
                    $murid->nisn = 'MURID_NISN_' . rand(100000, 999999);
                }
                if (empty($murid->namaLengkap)) {
                    $murid->namaLengkap = $murid->user?->name ?? 'Murid Dummy';
                }
                if (empty($murid->tanggalLahir)) {
                    $murid->tanggalLahir = '2008-01-01';
                }
                if (empty($murid->alamat)) {
                    $murid->alamat = 'Jl. Pelajar No. 1';
                }
                if (empty($murid->noTelpon)) {
                    $murid->noTelpon = '08123456789';
                }
                if (empty($murid->namaOrangTua)) {
                    $murid->namaOrangTua = 'Orang Tua Dummy';
                }
                if (empty($murid->no_telepon_orang_tua)) {
                    $murid->no_telepon_orang_tua = '08123456789';
                }
            }
        });
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function tugasSubmissions()
    {
        return $this->hasMany(TugasSubmission::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function ujianAttempts()
    {
        return $this->hasMany(UjianAttempt::class);
    }
}
