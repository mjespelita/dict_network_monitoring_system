
@extends('layouts.main')

@section('content')
    <h1>Overviewdiagrams Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>TotalGatewayNum</th>
            <td>{{ $item->totalGatewayNum }}</td>
        </tr>
    
        <tr>
            <th>ConnectedGatewayNum</th>
            <td>{{ $item->connectedGatewayNum }}</td>
        </tr>
    
        <tr>
            <th>DisconnectedGatewayNum</th>
            <td>{{ $item->disconnectedGatewayNum }}</td>
        </tr>
    
        <tr>
            <th>TotalSwitchNum</th>
            <td>{{ $item->totalSwitchNum }}</td>
        </tr>
    
        <tr>
            <th>ConnectedSwitchNum</th>
            <td>{{ $item->connectedSwitchNum }}</td>
        </tr>
    
        <tr>
            <th>DisconnectedSwitchNum</th>
            <td>{{ $item->disconnectedSwitchNum }}</td>
        </tr>
    
        <tr>
            <th>TotalPorts</th>
            <td>{{ $item->totalPorts }}</td>
        </tr>
    
        <tr>
            <th>AvailablePorts</th>
            <td>{{ $item->availablePorts }}</td>
        </tr>
    
        <tr>
            <th>PowerConsumption</th>
            <td>{{ $item->powerConsumption }}</td>
        </tr>
    
        <tr>
            <th>TotalApNum</th>
            <td>{{ $item->totalApNum }}</td>
        </tr>
    
        <tr>
            <th>ConnectedApNum</th>
            <td>{{ $item->connectedApNum }}</td>
        </tr>
    
        <tr>
            <th>IsolatedApNum</th>
            <td>{{ $item->isolatedApNum }}</td>
        </tr>
    
        <tr>
            <th>DisconnectedApNum</th>
            <td>{{ $item->disconnectedApNum }}</td>
        </tr>
    
        <tr>
            <th>TotalClientNum</th>
            <td>{{ $item->totalClientNum }}</td>
        </tr>
    
        <tr>
            <th>WiredClientNum</th>
            <td>{{ $item->wiredClientNum }}</td>
        </tr>
    
        <tr>
            <th>WirelessClientNum</th>
            <td>{{ $item->wirelessClientNum }}</td>
        </tr>
    
        <tr>
            <th>GuestNum</th>
            <td>{{ $item->guestNum }}</td>
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

    <a href='{{ route('overviewdiagrams.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
