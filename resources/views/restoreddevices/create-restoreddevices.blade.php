
@extends('layouts.main')

@section('content')
    <h1>Create a new restoreddevices</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('restoreddevices.store') }}' method='POST'>
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
    
        <div class='form-group'>
            <label for='name'>Ticket_number</label>
            <input type='text' class='form-control' id='ticket_number' name='ticket_number' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Reason</label>
            <input type='text' class='form-control' id='reason' name='reason' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Troubleshoot</label>
            <input type='text' class='form-control' id='troubleshoot' name='troubleshoot' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
