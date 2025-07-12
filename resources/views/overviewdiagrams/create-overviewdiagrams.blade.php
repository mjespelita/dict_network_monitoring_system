
@extends('layouts.main')

@section('content')
    <h1>Create a new overviewdiagrams</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('overviewdiagrams.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>TotalGatewayNum</label>
            <input type='text' class='form-control' id='totalGatewayNum' name='totalGatewayNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedGatewayNum</label>
            <input type='text' class='form-control' id='connectedGatewayNum' name='connectedGatewayNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedGatewayNum</label>
            <input type='text' class='form-control' id='disconnectedGatewayNum' name='disconnectedGatewayNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalSwitchNum</label>
            <input type='text' class='form-control' id='totalSwitchNum' name='totalSwitchNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedSwitchNum</label>
            <input type='text' class='form-control' id='connectedSwitchNum' name='connectedSwitchNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedSwitchNum</label>
            <input type='text' class='form-control' id='disconnectedSwitchNum' name='disconnectedSwitchNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalPorts</label>
            <input type='text' class='form-control' id='totalPorts' name='totalPorts' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>AvailablePorts</label>
            <input type='text' class='form-control' id='availablePorts' name='availablePorts' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>PowerConsumption</label>
            <input type='text' class='form-control' id='powerConsumption' name='powerConsumption' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalApNum</label>
            <input type='text' class='form-control' id='totalApNum' name='totalApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedApNum</label>
            <input type='text' class='form-control' id='connectedApNum' name='connectedApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>IsolatedApNum</label>
            <input type='text' class='form-control' id='isolatedApNum' name='isolatedApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedApNum</label>
            <input type='text' class='form-control' id='disconnectedApNum' name='disconnectedApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TotalClientNum</label>
            <input type='text' class='form-control' id='totalClientNum' name='totalClientNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>WiredClientNum</label>
            <input type='text' class='form-control' id='wiredClientNum' name='wiredClientNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>WirelessClientNum</label>
            <input type='text' class='form-control' id='wirelessClientNum' name='wirelessClientNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>GuestNum</label>
            <input type='text' class='form-control' id='guestNum' name='guestNum' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
