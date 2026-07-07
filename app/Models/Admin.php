<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'nip', 'fullname'])]
class Admin extends Model
{
    use HasFactory, SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected static function booted()
    {
        static::creating(function ($admin) {
            if (app()->runningUnitTests()) {
                if (empty($admin->nip)) {
                    $admin->nip = '197001011995031001';
                }
                if (empty($admin->fullname)) {
                    $admin->fullname = $admin->user?->name ?? 'Admin Dummy';
                }
            }
        });
    }
}
