@extends('emails.layout')

@section('content')
<div class="header" style="background: #dc3545;">
    <h2 style="margin:0;">Demande refusée</h2>
</div>
<div class="body">
    <p>Bonjour <strong>{{ $reservation->demandeur->name }}</strong>,</p>
    <p>Votre demande de réservation a été <strong>refusée</strong>.</p>

    <div class="detail-row"><span class="detail-label">Salle :</span> {{ $reservation->salle->nom }}</div>
    <div class="detail-row"><span class="detail-label">Date début :</span> {{ \Carbon\Carbon::parse($reservation->date_debut)->format('d/m/Y H:i') }}</div>
    <div class="detail-row"><span class="detail-label">Date fin :</span> {{ \Carbon\Carbon::parse($reservation->date_fin)->format('d/m/Y H:i') }}</div>

    <div class="motif-box" style="border-color: #dc3545;">
        <strong>Motif du refus :</strong> {{ $reservation->motif }}
    </div>

    <p style="margin-top:16px;">Vous pouvez soumettre une nouvelle demande pour un autre créneau.</p>
</div>
@endsection
