
@extends('layouts.main')

@section('content')
    <h1>Edit Clients</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('clients.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Mac_address</label>
            <input type='text' class='form-control' id='mac_address' name='mac_address' value='{{ $item->mac_address }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Device_name</label>
            <input type='text' class='form-control' id='device_name' name='device_name' value='{{ $item->device_name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Device_type</label>
            <input type='text' class='form-control' id='device_type' name='device_type' value='{{ $item->device_type }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Connected_device_type</label>
            <input type='text' class='form-control' id='connected_device_type' name='connected_device_type' value='{{ $item->connected_device_type }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Switch_name</label>
            <input type='text' class='form-control' id='switch_name' name='switch_name' value='{{ $item->switch_name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Port</label>
            <input type='text' class='form-control' id='port' name='port' value='{{ $item->port }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Standard_port</label>
            <input type='text' class='form-control' id='standard_port' name='standard_port' value='{{ $item->standard_port }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Network_theme</label>
            <input type='text' class='form-control' id='network_theme' name='network_theme' value='{{ $item->network_theme }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Uptime</label>
            <input type='text' class='form-control' id='uptime' name='uptime' value='{{ $item->uptime }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Traffic_down</label>
            <input type='text' class='form-control' id='traffic_down' name='traffic_down' value='{{ $item->traffic_down }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Traffic_up</label>
            <input type='text' class='form-control' id='traffic_up' name='traffic_up' value='{{ $item->traffic_up }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Status</label>
            <input type='text' class='form-control' id='status' name='status' value='{{ $item->status }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' value='{{ $item->siteId }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
