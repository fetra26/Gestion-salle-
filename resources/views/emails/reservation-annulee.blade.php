@extends('emails.layout')

@section('content')
<div class="header" style="background: #6c757d;">
    <h2 style="margin:0;">Réservation annulée</h2>
</div>
<div class="body">
    <p>Bonjour <strong>{{ $reservation->demandeur->name }}</strong>,</p>
    <p>Votre réservation a été <strong>annulée</strong>.</p>

    <div class="detail-row"><span class="detail-label">Salle :</span> {{ $reservation->salle->nom }}</div>
    <div class="detail-row"><span class="detail-label">Date début :</span> {{ \Carbon\Carbon::parse($reservation->date_debut)->format('d/m/Y H:i') }}</div>
    <div class="detail-row"><span class="detail-label">Date fin :</span> {{ \Carbon\Carbon::parse($reservation->date_fin)->format('d/m/Y H:i') }}</div>

    @if($reservation->motif)
    <div class="motif-box">
        <strong>Motif de l'annulation :</strong> {{ $reservation->motif }}
    </div>
    @endif
</div>
@endsection
