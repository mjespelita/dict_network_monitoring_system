
@extends('layouts.main')

@section('content')
    <h1>Devices - {{ $item->name }}</h1>

    <!-- Sidebar for Desktop View -->
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
        <a href='{{ url('insights/'.$item->siteId) }}' class='{{ request()->is('insights/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-chart-line"></i> Insights
        </a>
        <a href='{{ url('logs/'.$item->siteId) }}' class='{{ request()->is('logs/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-clipboard-list"></i> Logs
        </a>
        <a href='{{ url('customers') }}' class='{{ request()->is('customers', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </div>

    <div class='card'>
        <div class='card-body'>

            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>SFP WAN/LAN1</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>SFP WAN/LAN2</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>WAN3</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>WAN/LAN4</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN5</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN6</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN7</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN8</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN9</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN10</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN11</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN12</button>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <form action='{{ url('/customers-filter') }}' method='get'>
                        <div class='input-group'>
                            <input type='date' class='form-control' id='from' name='from' required> 
                            <b class='pt-2'>- to -</b>
                            <input type='date' class='form-control' id='to' name='to' required>
                            <div class='input-group-append'>
                                <button type='submit' class='btn btn-primary form-control'><i class='fas fa-filter'></i> Filter</button>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
            </div>

            <div class='table-responsive mt-5'>
                <table class='table table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Device Name</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Model</th>
                            <th>Version</th>
                            <th>Uptime</th>
                        </tr>
                    </thead>
                
                    <tbody>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-065 Allen Plaza</td>
                            <td>192.168.0.1</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.0</td>
                            <td>1.2.0</td>
                            <td>3day(s) 21h 12m 46s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-066 Hilltop Mall</td>
                            <td>192.168.0.2</td>
                            <td>
                                <span class="text-danger">Disconnected</span>
                            </td>
                            <td>ER7212PC v2.0</td>
                            <td>1.3.0</td>
                            <td>1day(s) 12h 15m 23s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-067 East River</td>
                            <td>192.168.0.3</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.1</td>
                            <td>1.2.5</td>
                            <td>2day(s) 15h 8m 11s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-068 Greenfield Park</td>
                            <td>192.168.0.4</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.2</td>
                            <td>1.4.0</td>
                            <td>4day(s) 18h 45m 33s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-069 Riverside Tower</td>
                            <td>192.168.0.5</td>
                            <td>
                                <span class="text-danger">Disconnected</span>
                            </td>
                            <td>ER7212PC v2.0</td>
                            <td>1.3.5</td>
                            <td>5day(s) 10h 29m 15s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-070 Central Plaza</td>
                            <td>192.168.0.6</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.3</td>
                            <td>1.2.8</td>
                            <td>6day(s) 4h 59m 30s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-071 Oceanview</td>
                            <td>192.168.0.7</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.1</td>
                            <td>1.2.2</td>
                            <td>7day(s) 13h 17m 28s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-072 Mountainview</td>
                            <td>192.168.0.8</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v2.0</td>
                            <td>1.4.1</td>
                            <td>8day(s) 10h 39m 58s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-073 Greenhill</td>
                            <td>192.168.0.9</td>
                            <td>
                                <span class="text-danger">Disconnected</span>
                            </td>
                            <td>ER7212PC v2.1</td>
                            <td>1.5.0</td>
                            <td>9day(s) 22h 22m 55s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-074 Coastal Bay</td>
                            <td>192.168.0.10</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.0</td>
                            <td>1.1.8</td>
                            <td>10day(s) 3h 41m 10s</td>
                        </tr>
                    </tbody>
                </table>
                
            </div>

        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
