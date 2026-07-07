<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['kode_pelajaran', 'nama_pelajaran', 'jurusan_id', 'is_active', 'admin_id'])]
class MataPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected $table = 'mata_pelajarans';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class)->withTrashed();
    }

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mata_pelajaran', 'mata_pelajaran_id', 'guru_id')->withTimestamps();
    }
}
