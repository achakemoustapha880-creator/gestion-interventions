@php
    $nbInterventions = \App\Models\Intervention::onlyTrashed()->count();
    $nbClients       = \App\Models\Client::onlyTrashed()->count();
@endphp

<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('interventions.corbeille') ? 'active' : '' }}"
           href="{{ route('interventions.corbeille') }}">
            <i class="bi bi-clipboard2-check me-1"></i>Interventions
            <span class="badge rounded-pill bg-secondary ms-1">{{ $nbInterventions }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('clients.corbeille') ? 'active' : '' }}"
           href="{{ route('clients.corbeille') }}">
            <i class="bi bi-people me-1"></i>Clients
            <span class="badge rounded-pill bg-secondary ms-1">{{ $nbClients }}</span>
        </a>
    </li>
</ul>