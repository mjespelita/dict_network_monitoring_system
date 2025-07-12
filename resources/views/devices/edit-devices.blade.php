
@extends('layouts.main')

@section('content')
    <h1>Edit Devices</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('devices.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Device_name</label>
            <input type='text' class='form-control' id='device_name' name='device_name' value='{{ $item->device_name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Ip_address</label>
            <input type='text' class='form-control' id='ip_address' name='ip_address' value='{{ $item->ip_address }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Status</label>
            <input type='text' class='form-control' id='status' name='status' value='{{ $item->status }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Model</label>
            <input type='text' class='form-control' id='model' name='model' value='{{ $item->model }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Version</label>
            <input type='text' class='form-control' id='version' name='version' value='{{ $item->version }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Uptime</label>
            <input type='text' class='form-control' id='uptime' name='uptime' value='{{ $item->uptime }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Cpu</label>
            <input type='text' class='form-control' id='cpu' name='cpu' value='{{ $item->cpu }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Memory</label>
            <input type='text' class='form-control' id='memory' name='memory' value='{{ $item->memory }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Public_ip</label>
            <input type='text' class='form-control' id='public_ip' name='public_ip' value='{{ $item->public_ip }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Link_speed</label>
            <input type='text' class='form-control' id='link_speed' name='link_speed' value='{{ $item->link_speed }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Duplex</label>
            <input type='text' class='form-control' id='duplex' name='duplex' value='{{ $item->duplex }}' required>
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
