<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'salle_id',
        'demandeur_id',
        'responsable_id',
        'date_debut',
        'date_fin',
        'statut',
        'motif',
        'description',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_CONFIRMEE = 'confirmee';
    public const STATUT_REFUSEE = 'refusee';
    public const STATUT_ANNULEE = 'annulee';
    public const STATUT_TERMINEE = 'terminee';

    public static array $statuts = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_CONFIRMEE => 'Confirmée',
        self::STATUT_REFUSEE => 'Refusée',
        self::STATUT_ANNULEE => 'Annulée',
        self::STATUT_TERMINEE => 'Terminée',
    ];

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function scopeConfirmee($query)
    {
        return $query->where('statut', self::STATUT_CONFIRMEE);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    public function scopeRefusee($query)
    {
        return $query->where('statut', self::STATUT_REFUSEE);
    }

    public function scopeAnnulee($query)
    {
        return $query->where('statut', self::STATUT_ANNULEE);
    }

    public function scopeTerminee($query)
    {
        return $query->where('statut', self::STATUT_TERMINEE);
    }

    public function peutEtreConfirmee(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    public function peutEtreRefusee(): bool
    {
        return in_array($this->statut, [self::STATUT_EN_ATTENTE, self::STATUT_CONFIRMEE]);
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [self::STATUT_EN_ATTENTE, self::STATUT_CONFIRMEE]);
    }

    public function chevauche(DateTime $debut, DateTime $fin, ?int $exceptId = null): bool
    {
        $query = $this->salle()
            ->where('salle_id', $this->salle_id)
            ->where('statut', self::STATUT_CONFIRMEE)
            ->where(function ($q) use ($debut, $fin) {
                $q->whereBetween('date_debut', [$debut, $fin])
                    ->orWhereBetween('date_fin', [$debut, $fin])
                    ->orWhere(function ($q) use ($debut, $fin) {
                        $q->where('date_debut', '<=', $debut)
                            ->where('date_fin', '>=', $fin);
                    });
            });

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}