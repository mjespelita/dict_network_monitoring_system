
@extends('layouts.main')

@section('content')
    <h1>Edit Tickets</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('tickets.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Sites_id</label>
            <input type='text' class='form-control' id='sites_id' name='sites_id' value='{{ $item->sites_id }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Ticket_number</label>
            <input type='text' class='form-control' id='ticket_number' name='ticket_number' value='{{ $item->ticket_number }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Date_reported</label>
            <input type='text' class='form-control' id='date_reported' name='date_reported' value='{{ $item->date_reported }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Address</label>
            <input type='text' class='form-control' id='address' name='address' value='{{ $item->address }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Nearest_landmark</label>
            <input type='text' class='form-control' id='nearest_landmark' name='nearest_landmark' value='{{ $item->nearest_landmark }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Issue</label>
            <input type='text' class='form-control' id='issue' name='issue' value='{{ $item->issue }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Troubleshooting1</label>
            <input type='text' class='form-control' id='troubleshooting1' name='troubleshooting1' value='{{ $item->troubleshooting1 }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
