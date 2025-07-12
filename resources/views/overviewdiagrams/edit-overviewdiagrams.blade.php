
@extends('layouts.main')

@section('content')
    <h1>Edit Overviewdiagrams</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('overviewdiagrams.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>TotalGatewayNum</label>
            <input type='text' class='form-control' id='totalGatewayNum' name='totalGatewayNum' value='{{ $item->totalGatewayNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedGatewayNum</label>
            <input type='text' class='form-control' id='connectedGatewayNum' name='connectedGatewayNum' value='{{ $item->connectedGatewayNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedGatewayNum</label>
            <input type='text' class='form-control' id='disconnectedGatewayNum' name='disconnectedGatewayNum' value='{{ $item->disconnectedGatewayNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalSwitchNum</label>
            <input type='text' class='form-control' id='totalSwitchNum' name='totalSwitchNum' value='{{ $item->totalSwitchNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedSwitchNum</label>
            <input type='text' class='form-control' id='connectedSwitchNum' name='connectedSwitchNum' value='{{ $item->connectedSwitchNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedSwitchNum</label>
            <input type='text' class='form-control' id='disconnectedSwitchNum' name='disconnectedSwitchNum' value='{{ $item->disconnectedSwitchNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalPorts</label>
            <input type='text' class='form-control' id='totalPorts' name='totalPorts' value='{{ $item->totalPorts }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>AvailablePorts</label>
            <input type='text' class='form-control' id='availablePorts' name='availablePorts' value='{{ $item->availablePorts }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>PowerConsumption</label>
            <input type='text' class='form-control' id='powerConsumption' name='powerConsumption' value='{{ $item->powerConsumption }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalApNum</label>
            <input type='text' class='form-control' id='totalApNum' name='totalApNum' value='{{ $item->totalApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedApNum</label>
            <input type='text' class='form-control' id='connectedApNum' name='connectedApNum' value='{{ $item->connectedApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>IsolatedApNum</label>
            <input type='text' class='form-control' id='isolatedApNum' name='isolatedApNum' value='{{ $item->isolatedApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedApNum</label>
            <input type='text' class='form-control' id='disconnectedApNum' name='disconnectedApNum' value='{{ $item->disconnectedApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalClientNum</label>
            <input type='text' class='form-control' id='totalClientNum' name='totalClientNum' value='{{ $item->totalClientNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>WiredClientNum</label>
            <input type='text' class='form-control' id='wiredClientNum' name='wiredClientNum' value='{{ $item->wiredClientNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>WirelessClientNum</label>
            <input type='text' class='form-control' id='wirelessClientNum' name='wirelessClientNum' value='{{ $item->wirelessClientNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>GuestNum</label>
            <input type='text' class='form-control' id='guestNum' name='guestNum' value='{{ $item->guestNum }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
