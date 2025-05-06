@extends('layouts.main')

@section('content')
    <h1>Dashboard</h1>
    <b>Hello, Admin</b>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sample Box 1</h5>
                    <p class="card-text">Track the status of inventorasdsa.</p>
                    <div id="lineChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sample Box 2</h5>
                    <p class="card-text">Track the status of inventorasdsa.</p>
                    <div id="barChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sample Box 1</h5>
                    <p class="card-text">Track the status of inventorasdsa.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sample Box 2</h5>
                    <p class="card-text">Track the status of inventorasdsa.</p>
                </div>
            </div>
        </div>
    </div>
@endsection