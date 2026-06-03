<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::deleted(function ($user) {
            $user->admin?->delete();
            $user->guru?->delete();
            $user->murid?->delete();
        });

        static::restored(function ($user) {
            $user->admin()->withTrashed()->first()?->restore();
            $user->guru()->withTrashed()->first()?->restore();
            $user->murid()->withTrashed()->first()?->restore();
        });

        static::forceDeleted(function ($user) {
            $user->admin()->withTrashed()->first()?->forceDelete();
            $user->guru()->withTrashed()->first()?->forceDelete();
            $user->murid()->withTrashed()->first()?->forceDelete();
        });
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function murid()
    {
        return $this->hasOne(Murid::class);
    }
}
