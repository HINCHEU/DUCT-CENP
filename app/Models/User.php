<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'p_id',
        'name',
        'email',
        'password',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'user_sites', 'user_id', 'site_id')
                    ->withPivot('assigned_from')
                    ->withTimestamps();
    }

    public function managedSites()
    {
        return $this->hasMany(Site::class, 'manager_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }
}
