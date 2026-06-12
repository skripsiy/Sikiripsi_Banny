<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['tahun_ajaran', 'is_active'])]
class TahunAkademik extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_akademiks';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'tahun_akademik_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'tahun_akademik_id');
    }

    public function getSemesterAttribute()
    {
        return $this->semesters()->where('is_active', true)->value('semester') 
            ?? $this->semesters()->value('semester') 
            ?? 'ganjil';
    }
}
