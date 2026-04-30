<?php

namespace App\Models;

use Database\Factories\SalleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'capacite',
        'description',
        'equipement',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'capacite' => 'integer',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationsConfirmees(): HasMany
    {
        return $this->hasMany(Reservation::class)->where('statut', 'confirmee');
    }
}