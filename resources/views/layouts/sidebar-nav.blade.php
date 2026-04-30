<div class="py-2">
    @role('responsable|admin')
    <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i> Tableau de bord
    </a>
    @endrole
    <a href="{{ route('calendrier') }}" class="sidebar-nav-link {{ request()->routeIs('calendrier') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Calendrier
    </a>
    <a href="{{ route('reservations.mes-reservations') }}" class="sidebar-nav-link {{ request()->routeIs('reservations.mes-reservations') ? 'active' : '' }}">
        <i class="bi bi-journal-check"></i> Mes réservations
    </a>
    <button type="button"
            class="sidebar-nav-link w-100 text-start border-0 bg-transparent"
            data-bs-toggle="modal" data-bs-target="#modalNouvelleReservation">
        <i class="bi bi-plus-circle"></i> Nouvelle demande
    </button>

    @can('peutValider')
    <hr class="border-secondary mx-3 my-2">
    <small class="text-muted px-3 d-block mb-1" style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase">Responsable</small>
    <a href="{{ route('reservations.index') }}" class="sidebar-nav-link {{ request()->routeIs('reservations.index') ? 'active' : '' }}">
        <i class="bi bi-list-ul"></i> Toutes les réservations
    </a>
    <a href="{{ route('reservations.a-valider') }}" class="sidebar-nav-link {{ request()->routeIs('reservations.a-valider') ? 'active' : '' }}">
        <i class="bi bi-check2-circle"></i>
        À valider
        @php($enAttente = \App\Models\Reservation::where('statut', 'en_attente')->count())
        @if($enAttente > 0)
        <span class="badge bg-danger ms-auto">{{ $enAttente }}</span>
        @endif
    </a>
    <a href="{{ route('reservations.directe.create') }}" class="sidebar-nav-link {{ request()->routeIs('reservations.directe.create') ? 'active' : '' }}">
        <i class="bi bi-lightning"></i> Réservation directe
    </a>
    @endcan

    @role('responsable|admin')
    <hr class="border-secondary mx-3 my-2">
    <small class="text-muted px-3 d-block mb-1" style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase">Administration</small>
    <a href="{{ route('salles.index') }}" class="sidebar-nav-link {{ request()->routeIs('salles.*') ? 'active' : '' }}">
        <i class="bi bi-building"></i> Salles
    </a>
    @endrole

    @role('admin')
    <a href="{{ route('admin.users.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Utilisateurs
    </a>
    <a href="{{ route('admin.directions.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.directions.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i> Directions
    </a>
    @endrole

    <hr class="border-secondary mx-3 my-2">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-nav-link w-100 text-start border-0 bg-transparent">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </button>
    </form>
</div>
