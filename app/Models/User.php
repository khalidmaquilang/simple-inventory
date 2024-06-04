<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasPanelShield, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * @return void
     */
    protected static function booted()
    {
        if (config('filament-shield.member_user.enabled', false)) {
            FilamentShield::createRole(name: config('filament-shield.member_user.name'));
            static::created(fn ($user) => $user->assignRole(config('filament-shield.member_user.name')));
            static::deleting(fn ($user) => $user->removeRole(config('filament-shield.member_user.name')));
        }
    }

    /**
     * @param  Panel  $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
