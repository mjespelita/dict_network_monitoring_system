
@extends('layouts.main')

@section('content')
    <h1>Clients - {{ $item->name }}</h1>

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
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>ALL</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>WIRELESS</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>WIRED</button>
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
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Device MAC Address</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Network SSID</th>
                            <th>Access Point/Port</th>
                            <th>Download Speed</th>
                            <th>Total Download</th>
                            <th>Total Upload</th>
                            <th>Connection Uptime</th>
                        </tr>
                    </thead>
                
                    <tbody>
                        <tr>
                            <th scope="row">
                                <img src="{{ url('/assets/phone.jpg') }}" alt="" width="80px">
                            </th>
                            <td>06-5C-BE-A8-4B-85</td>
                            <td>192.168.0.12</td>
                            <td>
                                <span class="text-success">AUTHORIZED</span>
                            </td>
                            <td>DICT Freewifi4all</td>
                            <td>7C-F1-7E-F6-7C-3C</td>
                            <td>16.88 KB/s</td>
                            <td>182.56 MB</td>
                            <td>17.90 MB</td>
                            <td>49m 34s</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <img src="{{ url('/assets/laptop.jpg') }}" alt="" width="80px">
                            </th>
                            <td>06-5C-BE-A8-4B-86</td>
                            <td>192.168.0.13</td>
                            <td>
                                <span class="text-danger">DISCONNECTED</span>
                            </td>
                            <td>DICT Freewifi4all</td>
                            <td>7C-F1-7E-F6-7C-3D</td>
                            <td>12.34 KB/s</td>
                            <td>150.00 MB</td>
                            <td>8.56 MB</td>
                            <td>1h 12m 45s</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <img src="{{ url('/assets/phone.jpg') }}" alt="" width="80px">
                            </th>
                            <td>06-5C-BE-A8-4B-87</td>
                            <td>192.168.0.14</td>
                            <td>
                                <span class="text-success">AUTHORIZED</span>
                            </td>
                            <td>DICT Freewifi4all</td>
                            <td>7C-F1-7E-F6-7C-3E</td>
                            <td>20.56 KB/s</td>
                            <td>200.23 MB</td>
                            <td>18.75 MB</td>
                            <td>2h 5m 10s</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <img src="{{ url('/assets/phone.jpg') }}" alt="" width="80px">
                            </th>
                            <td>06-5C-BE-A8-4B-88</td>
                            <td>192.168.0.15</td>
                            <td>
                                <span class="text-success">AUTHORIZED</span>
                            </td>
                            <td>DICT Freewifi4all</td>
                            <td>7C-F1-7E-F6-7C-3F</td>
                            <td>14.22 KB/s</td>
                            <td>175.34 MB</td>
                            <td>10.30 MB</td>
                            <td>3h 15m 30s</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <img src="{{ url('/assets/laptop.jpg') }}" alt="" width="80px">
                            </th>
                            <td>06-5C-BE-A8-4B-89</td>
                            <td>192.168.0.16</td>
                            <td>
                                <span class="text-danger">DISCONNECTED</span>
                            </td>
                            <td>DICT Freewifi4all</td>
                            <td>7C-F1-7E-F6-7C-40</td>
                            <td>8.10 KB/s</td>
                            <td>125.67 MB</td>
                            <td>5.45 MB</td>
                            <td>45m 22s</td>
                        </tr>
                    </tbody>
                </table>
                
                
            </div>

        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
