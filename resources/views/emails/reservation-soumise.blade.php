@extends('emails.layout')

@section('content')
<div class="header" style="background: #0d6efd;">
    <h2 style="margin:0;">Demande soumise</h2>
</div>
<div class="body">
    <p>Bonjour <strong>{{ $reservation->demandeur->name }}</strong>,</p>
    <p>Votre demande de réservation a bien été enregistrée et est en attente de validation.</p>

    <div class="detail-row"><span class="detail-label">Salle :</span> {{ $reservation->salle->nom }}</div>
    <div class="detail-row"><span class="detail-label">Date début :</span> {{ \Carbon\Carbon::parse($reservation->date_debut)->format('d/m/Y H:i') }}</div>
    <div class="detail-row"><span class="detail-label">Date fin :</span> {{ \Carbon\Carbon::parse($reservation->date_fin)->format('d/m/Y H:i') }}</div>
    @if($reservation->description)
    <div class="detail-row"><span class="detail-label">Description :</span> {{ $reservation->description }}</div>
    @endif

    <p style="margin-top:16px;">Vous serez notifié par email lorsque votre demande sera traitée.</p>
</div>
@endsection
