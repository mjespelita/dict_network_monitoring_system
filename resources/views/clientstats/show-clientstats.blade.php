
@extends('layouts.main')

@section('content')
    <h1>Clientstats Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Total</th>
            <td>{{ $item->total }}</td>
        </tr>
    
        <tr>
            <th>Wireless</th>
            <td>{{ $item->wireless }}</td>
        </tr>
    
        <tr>
            <th>Wired</th>
            <td>{{ $item->wired }}</td>
        </tr>
    
        <tr>
            <th>Num2g</th>
            <td>{{ $item->num2g }}</td>
        </tr>
    
        <tr>
            <th>Num5g</th>
            <td>{{ $item->num5g }}</td>
        </tr>
    
        <tr>
            <th>Num6g</th>
            <td>{{ $item->num6g }}</td>
        </tr>
    
        <tr>
            <th>NumUser</th>
            <td>{{ $item->numUser }}</td>
        </tr>
    
        <tr>
            <th>NumGuest</th>
            <td>{{ $item->numGuest }}</td>
        </tr>
    
        <tr>
            <th>NumWirelessUser</th>
            <td>{{ $item->numWirelessUser }}</td>
        </tr>
    
        <tr>
            <th>NumWirelessGuest</th>
            <td>{{ $item->numWirelessGuest }}</td>
        </tr>
    
        <tr>
            <th>Num2gUser</th>
            <td>{{ $item->num2gUser }}</td>
        </tr>
    
        <tr>
            <th>Num5gUser</th>
            <td>{{ $item->num5gUser }}</td>
        </tr>
    
        <tr>
            <th>Num6gUser</th>
            <td>{{ $item->num6gUser }}</td>
        </tr>
    
        <tr>
            <th>Num2gGuest</th>
            <td>{{ $item->num2gGuest }}</td>
        </tr>
    
        <tr>
            <th>Num5gGuest</th>
            <td>{{ $item->num5gGuest }}</td>
        </tr>
    
        <tr>
            <th>Num6gGuest</th>
            <td>{{ $item->num6gGuest }}</td>
        </tr>
    
        <tr>
            <th>Poor</th>
            <td>{{ $item->poor }}</td>
        </tr>
    
        <tr>
            <th>Fair</th>
            <td>{{ $item->fair }}</td>
        </tr>
    
        <tr>
            <th>NoData</th>
            <td>{{ $item->noData }}</td>
        </tr>
    
        <tr>
            <th>Good</th>
            <td>{{ $item->good }}</td>
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

    <a href='{{ route('clientstats.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
