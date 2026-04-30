@extends('emails.layout')

@section('content')
<div class="header" style="background: #198754;">
    <h2 style="margin:0;">Réservation confirmée ✓</h2>
</div>
<div class="body">
    <p>Bonjour <strong>{{ $reservation->demandeur->name }}</strong>,</p>
    <p>Bonne nouvelle ! Votre réservation a été <strong>confirmée</strong>.</p>

    <div class="detail-row"><span class="detail-label">Salle :</span> {{ $reservation->salle->nom }}</div>
    <div class="detail-row"><span class="detail-label">Date début :</span> {{ \Carbon\Carbon::parse($reservation->date_debut)->format('d/m/Y H:i') }}</div>
    <div class="detail-row"><span class="detail-label">Date fin :</span> {{ \Carbon\Carbon::parse($reservation->date_fin)->format('d/m/Y H:i') }}</div>
    @if($reservation->responsable)
    <div class="detail-row"><span class="detail-label">Validé par :</span> {{ $reservation->responsable->name }}</div>
    @endif
    @if($reservation->motif)
    <div class="motif-box" style="border-color: #198754;">
        <strong>Note :</strong> {{ $reservation->motif }}
    </div>
    @endif
</div>
@endsection
