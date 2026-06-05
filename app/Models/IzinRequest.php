<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'learning_module_id',
    'murid_id',
    'date',
    'jenis_izin',
    'alasan',
    'bukti_file_path',
    'status',
    'catatan_guru'
])]
class IzinRequest extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
    ];

    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class);
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
}
