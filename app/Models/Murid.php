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
