
@extends('layouts.main')

@section('content')
    <h1>Clients Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Mac_address</th>
            <td>{{ $item->mac_address }}</td>
        </tr>
    
        <tr>
            <th>Device_name</th>
            <td>{{ $item->device_name }}</td>
        </tr>
    
        <tr>
            <th>Device_type</th>
            <td>{{ $item->device_type }}</td>
        </tr>
    
        <tr>
            <th>Connected_device_type</th>
            <td>{{ $item->connected_device_type }}</td>
        </tr>
    
        <tr>
            <th>Switch_name</th>
            <td>{{ $item->switch_name }}</td>
        </tr>
    
        <tr>
            <th>Port</th>
            <td>{{ $item->port }}</td>
        </tr>
    
        <tr>
            <th>Standard_port</th>
            <td>{{ $item->standard_port }}</td>
        </tr>
    
        <tr>
            <th>Network_theme</th>
            <td>{{ $item->network_theme }}</td>
        </tr>
    
        <tr>
            <th>Uptime</th>
            <td>{{ $item->uptime }}</td>
        </tr>
    
        <tr>
            <th>Traffic_down</th>
            <td>{{ $item->traffic_down }}</td>
        </tr>
    
        <tr>
            <th>Traffic_up</th>
            <td>{{ $item->traffic_up }}</td>
        </tr>
    
        <tr>
            <th>Status</th>
            <td>{{ $item->status }}</td>
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

    <a href='{{ route('clients.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
