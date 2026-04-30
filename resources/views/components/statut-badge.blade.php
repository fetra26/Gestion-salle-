@props(['statut'])
@php
$labels  = ['en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'refusee' => 'Refusée', 'annulee' => 'Annulée', 'terminee' => 'Terminée'];
$couleurs = ['en_attente' => 'warning', 'confirmee' => 'success', 'refusee' => 'danger', 'annulee' => 'secondary', 'terminee' => 'info'];
@endphp
<span class="badge bg-{{ $couleurs[$statut] ?? 'secondary' }}">
    {{ $labels[$statut] ?? $statut }}
</span>
