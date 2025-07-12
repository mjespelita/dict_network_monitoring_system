
@extends('layouts.main')

@section('content')
    <h1>Create a new clientdetails</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('clientdetails.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Mac</label>
            <input type='text' class='form-control' id='mac' name='mac' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DeviceType</label>
            <input type='text' class='form-control' id='deviceType' name='deviceType' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SwitchName</label>
            <input type='text' class='form-control' id='switchName' name='switchName' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SwitchMac</label>
            <input type='text' class='form-control' id='switchMac' name='switchMac' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Port</label>
            <input type='text' class='form-control' id='port' name='port' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>StandardPort</label>
            <input type='text' class='form-control' id='standardPort' name='standardPort' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TrafficDown</label>
            <input type='text' class='form-control' id='trafficDown' name='trafficDown' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>TrafficUp</label>
            <input type='text' class='form-control' id='trafficUp' name='trafficUp' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Uptime</label>
            <input type='text' class='form-control' id='uptime' name='uptime' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Guest</label>
            <input type='text' class='form-control' id='guest' name='guest' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Blocked</label>
            <input type='text' class='form-control' id='blocked' name='blocked' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
