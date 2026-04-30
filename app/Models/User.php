<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'direction_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'demandeur_id');
    }

    public function reservationsEnTantQueResponsable(): HasMany
    {
        return $this->hasMany(Reservation::class, 'responsable_id');
    }

    public function estUtilisateur(): bool
    {
        return $this->hasRole('utilisateur');
    }

    public function estResponsable(): bool
    {
        return $this->hasRole('responsable');
    }

    public function estAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function peutValider(): bool
    {
        return $this->estResponsable() || $this->estAdmin();
    }
}
