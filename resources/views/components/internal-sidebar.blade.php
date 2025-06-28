<div class='sidebar' id='mobileSidebar'>
    <div class='logo'>
        <div class="p-3">
            <img src='{{ url('assets/librify-logo.png') }}' alt=''> <br>
        </div>
        <div class="p-3">
            <small>Powered by</small>
            <img src='{{ url('assets/logo.png') }}' alt='' style="width: 60px !important">
        </div>
    </div>

    <div class="p-2">
        <button type='button' class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style="width: 100%;">
            {{ $item->name }}
        </button>
        <div class='dropdown-menu' style="width: 95%; border: 1px solid #212529">
            @forelse (App\Models\Sites::all() as $site)
                <a class='dropdown-item bulk-move-to-trash' href='{{ url('/show-sites/'.$site->siteId) }}'><i class='fa fa-eye'></i> {{ $site->name }}</a>
            @empty
                <p>No Sites</p>
            @endforelse
        </div>
    </div>

    <a href='{{ url('sites') }}'><i class='fas fa-arrow-left'></i> Back</a>
    <div class="p-2">
        <b class="text-secondary">Monitoring</b>
    </div>
    <a href='{{ url('show-sites/'.$item->siteId) }}' class='{{ request()->is('show-sites/*') ? 'active' : '' }}'><i class='fas fa-tachometer-alt'></i> Dashboard</a>
    <a href='{{ url('statistics/'.$item->siteId) }}' class='{{ request()->is('statistics/*', 'trash-statistics', 'create-statistics', 'show-statistics/*', 'edit-statistics/*', 'delete-statistics/*', 'statistics-search*') ? 'active' : '' }}'>
        <i class="fas fa-line-chart"></i> Statistics
    </a>
    <a href='{{ url('devices/'.$item->siteId) }}' class='{{ request()->is('devices/*', 'trash-devices', 'create-devices', 'show-devices/*', 'edit-devices/*', 'delete-devices/*', 'devices-search*') ? 'active' : '' }}'>
        <i class="fas fa-desktop"></i> Devices
    </a>
    <a href='{{ url('clients/'.$item->siteId) }}' class='{{ request()->is('clients/*', 'trash-clients', 'create-clients', 'show-clients/*', 'edit-clients/*', 'delete-clients/*', 'clients-search*') ? 'active' : '' }}'>
        <i class="fas fa-users"></i> Clients
    </a>
    {{-- <a href='{{ url('insights/'.$item->siteId) }}' class='{{ request()->is('insights/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
        <i class="fas fa-chart-line"></i> Insights
    </a> --}}
    <a href='{{ url('logs/'.$item->siteId) }}' class='{{ request()->is('logs/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
        <i class="fas fa-clipboard-list"></i> Logs
    </a>
    <a href='{{ url('tickets/'.$item->siteId) }}' class='{{ request()->is('tickets/*', 'trash-tickets', 'create-tickets', 'show-tickets/*', 'edit-tickets/*', 'delete-tickets/*', 'tickets-search*') ? 'active' : '' }}'>
        <i class="fas fa-file-alt"></i> Tickets
    </a>
</div>
