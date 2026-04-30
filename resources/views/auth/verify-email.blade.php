@extends('auth.layout')

@section('content')
<p class="text-muted small mb-3">
    Merci pour votre inscription ! Avant de continuer, veuillez vérifier votre adresse email
    en cliquant sur le lien que nous vous avons envoyé. Si vous n'avez pas reçu l'email,
    nous pouvons vous en envoyer un autre.
</p>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success">
        Un nouveau lien de vérification a été envoyé à votre adresse email.
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-primary w-100">Renvoyer l'email de vérification</button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-link p-0">Se déconnecter</button>
</form>
@endsection
