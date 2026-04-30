@extends('auth.layout')

@section('content')
<p class="text-muted small mb-3">
    Zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
</p>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror"
               id="password" name="password" required autocomplete="current-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">Confirmer</button>
</form>
@endsection
