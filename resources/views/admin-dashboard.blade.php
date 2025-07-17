@extends('layouts.main')

@section('content')
    <h1>Dashboard</h1>
    <b>Hello, {{ Auth::user()->name }} ({{ Auth::user()->role }})</b>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-primary">
                    <h5 class="card-title">Total Sites</h5>
                    <h1>
                        <i class="fas fa-house"></i> {{ App\Models\Sites::count() }}
                    </h1>

                    <a href="{{ url('/sites') }}"><button class="btn btn-outline-primary">View Details</button></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-danger">
                    <h5 class="card-title">Offline Sites</h5>
                    <h1>
                        <i class="fas fa-ban"></i> {{ App\Models\Incidents::count() }}
                    </h1>
                    <a href="{{ url('/incidents') }}"><button class="btn btn-outline-danger">View Details</button></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-danger">
                    <h5 class="card-title">Disconnected Devices</h5>
                    <h1>
                        <i class="fas fa-plug"></i> {{ App\Models\Disconnecteddevices::count() }}
                    </h1>
                    <a href="{{ url('/disconnecteddevices') }}"><button class="btn btn-outline-danger">View Details</button></a>
                </div>
            </div>
        </div>
        @if (Auth::user()->role === "admin")
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-success">
                        <h5 class="card-title">User Accounts</h5>
                        <h1>
                            <i class="fas fa-users"></i> {{ App\Models\User::count() }}
                        </h1>
                        <a href="{{ url('/useraccounts') }}"><button class="btn btn-outline-success">View Details</button></a>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-success">
                    <h5 class="card-title">Online Sites</h5>
                    <h1>
                        <i class="fas fa-wifi"></i> {{ App\Models\Sites::count() - App\Models\Incidents::count() }}
                    </h1>
                    <a href="{{ url('/sites') }}"><button class="btn btn-outline-success">View Details</button></a>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <h3>Sites</h3>
    <div class="card">
        <div class="card-body">
            <style>
                .custom-link {
                    color: #000; /* default text-dark */
                    font-weight: bold;
                    text-decoration: none;
                }

                .custom-link:hover {
                    color: var(--bs-primary); /* Bootstrap primary color */
                }
            </style>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Site Name</th>
                            <th>Status</th>
                            <th>APs</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (App\Models\Sites::orderBy('name', 'asc')->get() as $site)
                            <tr>
                                <td>
                                    <a href="{{ url('/show-sites/'.$site->siteId) }}" class="custom-link">{{ $site->name }}</a>
                                </td>
                                <td>
                                    @foreach ($siteStatuses as $siteStatus)
                                        @if ($siteStatus['siteId'] === $site->siteId)
                                            @if ($siteStatus['status'] === 1)
                                                <p class="badge bg-success"><i class="fas fa-wifi"></i> Online </p>
                                            @else
                                                <p class="badge bg-danger"><i class="fas fa-ban"></i> Offline </p>
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                                <td title="Connected / Disconnected">
                                    <b class="text-success">{{ App\Models\Devices::where('siteId', $site->siteId)->where('status', 1)->count() }}</b> / <b class="text-danger">{{ App\Models\Devices::where('siteId', $site->siteId)->where('status', 0)->count() }}</b>
                                </td>
                                <td>
                                    <a href="{{ url('/show-sites/'.$site->siteId) }}"><button class="btn btn-success"><i class="fas fa-house"></i> Visit</button></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>No Sites</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
