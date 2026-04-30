@extends('auth.layout')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Bienvenue</h4>
    <p class="text-muted small mb-0">Connectez-vous à votre espace</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

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
                   required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-medium mb-0">Mot de passe</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-muted text-decoration-none">Oublié ?</a>
            @endif
        </div>
        <div class="input-group has-validation">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-lock text-muted"></i>
            </span>
            <input type="password"
                   class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                   id="password" name="password"
                   placeholder="••••••••"
                   required autocomplete="current-password">
            <button type="button" class="btn btn-outline-secondary bg-white" id="togglePassword" tabindex="-1"
                    title="Afficher / masquer">
                <i class="bi bi-eye text-muted" id="togglePwdIcon"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-4 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember"
               {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label text-muted small" for="remember">Se souvenir de moi</label>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
    </button>
</form>


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
