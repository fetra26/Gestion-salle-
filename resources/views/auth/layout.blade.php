<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gestion Salles') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="min-vh-100 d-flex">

    {{-- ── Panneau gauche – branding ── --}}
    <div class="d-none d-lg-flex flex-column justify-content-between p-5 text-white"
         style="width:440px;flex-shrink:0;background:linear-gradient(155deg,#0a3880 0%,#1565c0 55%,#1e88e5 100%)">
        <div>
            <div class="d-flex align-items-center gap-3 mb-5">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:rgba(255,255,255,.18)">
                    <i class="bi bi-building-fill fs-4"></i>
                </div>
                <span class="fs-5 fw-bold">{{ config('app.name', 'Gestion Salles') }}</span>
            </div>
            <h2 class="fw-bold mb-3 lh-sm">Gérez vos salles de réunion facilement</h2>
            <p class="mb-5" style="opacity:.75;font-size:.95rem">
                Planification, validation et suivi de vos réservations en temps réel.
            </p>
            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                <li class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(255,255,255,.15)">
                        <i class="bi bi-calendar-check fs-6"></i>
                    </div>
                    <span class="small" style="opacity:.9">Réservation en quelques clics</span>
                </li>
                <li class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(255,255,255,.15)">
                        <i class="bi bi-shield-check fs-6"></i>
                    </div>
                    <span class="small" style="opacity:.9">Validation par les responsables</span>
                </li>
                <li class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(255,255,255,.15)">
                        <i class="bi bi-bell-fill fs-6"></i>
                    </div>
                    <span class="small" style="opacity:.9">Notifications email automatiques</span>
                </li>
            </ul>
        </div>
        <p class="small mb-0" style="opacity:.4">© {{ date('Y') }} {{ config('app.name') }}</p>
    </div>

    {{-- ── Panneau droit – formulaire ── --}}
    <div class="flex-grow-1 d-flex align-items-center justify-content-center p-4 bg-light">
        <div style="width:100%;max-width:420px">

            {{-- Logo mobile uniquement --}}
            <div class="d-flex d-lg-none align-items-center gap-2 mb-4 justify-content-center">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width:40px;height:40px;background:#1565c0">
                    <i class="bi bi-building-fill text-white fs-5"></i>
                </div>
                <span class="fs-5 fw-bold">{{ config('app.name', 'Gestion Salles') }}</span>
            </div>

            @if(session('status'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <ul class="mb-0 ps-0 list-unstyled">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
