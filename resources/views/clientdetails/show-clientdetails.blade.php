
@extends('layouts.main')

@section('content')
    <h1>Clientdetails Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Mac</th>
            <td>{{ $item->mac }}</td>
        </tr>
    
        <tr>
            <th>Name</th>
            <td>{{ $item->name }}</td>
        </tr>
    
        <tr>
            <th>DeviceType</th>
            <td>{{ $item->deviceType }}</td>
        </tr>
    
        <tr>
            <th>SwitchName</th>
            <td>{{ $item->switchName }}</td>
        </tr>
    
        <tr>
            <th>SwitchMac</th>
            <td>{{ $item->switchMac }}</td>
        </tr>
    
        <tr>
            <th>Port</th>
            <td>{{ $item->port }}</td>
        </tr>
    
        <tr>
            <th>StandardPort</th>
            <td>{{ $item->standardPort }}</td>
        </tr>
    
        <tr>
            <th>TrafficDown</th>
            <td>{{ $item->trafficDown }}</td>
        </tr>
    
        <tr>
            <th>TrafficUp</th>
            <td>{{ $item->trafficUp }}</td>
        </tr>
    
        <tr>
            <th>Uptime</th>
            <td>{{ $item->uptime }}</td>
        </tr>
    
        <tr>
            <th>Guest</th>
            <td>{{ $item->guest }}</td>
        </tr>
    
        <tr>
            <th>Blocked</th>
            <td>{{ $item->blocked }}</td>
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

    <a href='{{ route('clientdetails.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
