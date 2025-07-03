
@extends('layouts.main')

@section('content')
    <h1>Create a new disconnecteddevices</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('disconnecteddevices.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Device_name</label>
            <input type='text' class='form-control' id='device_name' name='device_name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Device_mac</label>
            <input type='text' class='form-control' id='device_mac' name='device_mac' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Device_type</label>
            <input type='text' class='form-control' id='device_type' name='device_type' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Status</label>
            <input type='text' class='form-control' id='status' name='status' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
