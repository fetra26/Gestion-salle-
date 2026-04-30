@extends('auth.layout')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Créer un compte</h4>
    <p class="text-muted small mb-0">Rejoignez votre espace de gestion</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label fw-medium">Nom complet</label>
        <div class="input-group has-validation">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-person text-muted"></i>
            </span>
            <input type="text"
                   class="form-control border-start-0 @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}"
                   placeholder="Jean Dupont"
                   required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-medium">Adresse email</label>
        <div class="input-group has-validation">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-envelope text-muted"></i>
            </span>
            <input type="email"
                   class="form-control border-start-0 @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}"
                   placeholder="votre@email.com"
                   required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-medium">Mot de passe</label>
        <div class="input-group has-validation">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-lock text-muted"></i>
            </span>
            <input type="password"
                   class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                   id="password" name="password"
                   placeholder="••••••••"
                   required autocomplete="new-password">
            <button type="button" class="btn btn-outline-secondary bg-white" id="togglePassword" tabindex="-1"
                    title="Afficher / masquer">
                <i class="bi bi-eye text-muted" id="togglePwdIcon"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-medium">Confirmer le mot de passe</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-lock-fill text-muted"></i>
            </span>
            <input type="password"
                   class="form-control border-start-0"
                   id="password_confirmation" name="password_confirmation"
                   placeholder="••••••••"
                   required autocomplete="new-password">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
        <i class="bi bi-person-plus me-2"></i>Créer mon compte
    </button>
</form>

<div class="text-center mt-4 pt-3 border-top">
    <span class="text-muted small">Déjà inscrit ?</span>
    <a href="{{ route('login') }}" class="small fw-semibold ms-1 text-decoration-none">Se connecter</a>
</div>

@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('togglePwdIcon');
    const show = pwd.type === 'password';
    pwd.type       = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash text-muted' : 'bi bi-eye text-muted';
});
</script>
@endpush
@endsection
