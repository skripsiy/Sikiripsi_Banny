<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectGuru extends Model
{
    protected $table = 'subject_guru';

    protected $fillable = ['subject_id', 'guru_id'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
