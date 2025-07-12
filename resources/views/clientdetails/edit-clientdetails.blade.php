
@extends('layouts.main')

@section('content')
    <h1>Edit Clientdetails</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('clientdetails.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Mac</label>
            <input type='text' class='form-control' id='mac' name='mac' value='{{ $item->mac }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DeviceType</label>
            <input type='text' class='form-control' id='deviceType' name='deviceType' value='{{ $item->deviceType }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SwitchName</label>
            <input type='text' class='form-control' id='switchName' name='switchName' value='{{ $item->switchName }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SwitchMac</label>
            <input type='text' class='form-control' id='switchMac' name='switchMac' value='{{ $item->switchMac }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Port</label>
            <input type='text' class='form-control' id='port' name='port' value='{{ $item->port }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>StandardPort</label>
            <input type='text' class='form-control' id='standardPort' name='standardPort' value='{{ $item->standardPort }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TrafficDown</label>
            <input type='text' class='form-control' id='trafficDown' name='trafficDown' value='{{ $item->trafficDown }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TrafficUp</label>
            <input type='text' class='form-control' id='trafficUp' name='trafficUp' value='{{ $item->trafficUp }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Uptime</label>
            <input type='text' class='form-control' id='uptime' name='uptime' value='{{ $item->uptime }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Guest</label>
            <input type='text' class='form-control' id='guest' name='guest' value='{{ $item->guest }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Blocked</label>
            <input type='text' class='form-control' id='blocked' name='blocked' value='{{ $item->blocked }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
