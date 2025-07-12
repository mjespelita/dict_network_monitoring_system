
@extends('layouts.main')

@section('content')
    <h1>Devices Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Device_name</th>
            <td>{{ $item->device_name }}</td>
        </tr>
    
        <tr>
            <th>Ip_address</th>
            <td>{{ $item->ip_address }}</td>
        </tr>
    
        <tr>
            <th>Status</th>
            <td>{{ $item->status }}</td>
        </tr>
    
        <tr>
            <th>Model</th>
            <td>{{ $item->model }}</td>
        </tr>
    
        <tr>
            <th>Version</th>
            <td>{{ $item->version }}</td>
        </tr>
    
        <tr>
            <th>Uptime</th>
            <td>{{ $item->uptime }}</td>
        </tr>
    
        <tr>
            <th>Cpu</th>
            <td>{{ $item->cpu }}</td>
        </tr>
    
        <tr>
            <th>Memory</th>
            <td>{{ $item->memory }}</td>
        </tr>
    
        <tr>
            <th>Public_ip</th>
            <td>{{ $item->public_ip }}</td>
        </tr>
    
        <tr>
            <th>Link_speed</th>
            <td>{{ $item->link_speed }}</td>
        </tr>
    
        <tr>
            <th>Duplex</th>
            <td>{{ $item->duplex }}</td>
        </tr>
    
        <tr>
            <th>SiteId</th>
            <td>{{ $item->siteId }}</td>
        </tr>
    
                    <tr>
                        <th>Created At</th>
                        <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->updated_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <a href='{{ route('devices.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
