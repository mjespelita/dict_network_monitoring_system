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
@endsection
