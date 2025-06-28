
@extends('layouts.main')

@section('content')
    <h1>Tickets Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Sites_id</th>
            <td>{{ $item->sites_id }}</td>
        </tr>
    
        <tr>
            <th>Ticket_number</th>
            <td>{{ $item->ticket_number }}</td>
        </tr>
    
        <tr>
            <th>Date_reported</th>
            <td>{{ $item->date_reported }}</td>
        </tr>
    
        <tr>
            <th>Name</th>
            <td>{{ $item->name }}</td>
        </tr>
    
        <tr>
            <th>Address</th>
            <td>{{ $item->address }}</td>
        </tr>
    
        <tr>
            <th>Nearest_landmark</th>
            <td>{{ $item->nearest_landmark }}</td>
        </tr>
    
        <tr>
            <th>Issue</th>
            <td>{{ $item->issue }}</td>
        </tr>
    
        <tr>
            <th>Troubleshooting1</th>
            <td>{{ $item->troubleshooting1 }}</td>
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

    <a href='{{ route('tickets.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
