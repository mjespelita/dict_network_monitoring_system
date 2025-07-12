
@extends('layouts.main')

@section('content')
    <h1>Lognotifications Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Key</th>
            <td>{{ $item->key }}</td>
        </tr>
    
        <tr>
            <th>ShortMsg</th>
            <td>{{ $item->shortMsg }}</td>
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

    <a href='{{ route('lognotifications.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
