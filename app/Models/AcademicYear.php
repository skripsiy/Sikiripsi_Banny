<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['tahun_ajaran', 'is_active'])]
class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'tahun_ajaran_id');
    }

    public function tahunAjarans()
    {
        return $this->hasMany(TahunAjaran::class, 'academic_year_id');
    }

    public function getSemesterAttribute()
    {
        return $this->tahunAjarans()->where('is_active', true)->value('semester') 
            ?? $this->tahunAjarans()->value('semester') 
            ?? 'ganjil';
    }
}
