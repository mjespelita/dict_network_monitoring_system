
@extends('layouts.main')

@section('content')
    <h1>Disconnecteddevices Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Name</th>
            <td>{{ $item->name }}</td>
        </tr>
    
        <tr>
            <th>Device_name</th>
            <td>{{ $item->device_name }}</td>
        </tr>
    
        <tr>
            <th>Device_mac</th>
            <td>{{ $item->device_mac }}</td>
        </tr>
    
        <tr>
            <th>Device_type</th>
            <td>{{ $item->device_type }}</td>
        </tr>
    
        <tr>
            <th>Status</th>
            <td>{{ $item->status }}</td>
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

    <a href='{{ route('disconnecteddevices.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
