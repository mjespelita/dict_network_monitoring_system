@extends('layouts.main')

@section('content')
    <h1>Dashboard</h1>
    <b>Hello, {{ Auth::user()->name }} ({{ Auth::user()->role }})</b>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sites</h5>
                    <h1>
                        <i class="fas fa-house"></i> 322
                    </h1>
                </div>
            </div>
        </div>
    </div>
@endsection
