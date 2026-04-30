@extends('auth.layout')

@section('content')
<p class="text-muted small mb-3">
    Mot de passe oublié ? Saisissez votre adresse email et nous vous enverrons un lien de réinitialisation.
</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror"
               id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="small">Retour à la connexion</a>
</div>
@endsection
