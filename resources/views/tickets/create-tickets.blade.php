
@extends('layouts.main')

@section('content')
    <h1>Create a new tickets</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('tickets.store') }}' method='POST'>
                @csrf

                <div class='form-group'>
                    <label for='name'>Sites_id</label>
                    <input type='text' class='form-control' id='sites_id' name='sites_id' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Ticket_number</label>
                    <input type='text' class='form-control' id='ticket_number' name='ticket_number' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Date_reported</label>
                    <input type='text' class='form-control' id='date_reported' name='date_reported' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Name</label>
                    <input type='text' class='form-control' id='name' name='name' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Address</label>
                    <input type='text' class='form-control' id='address' name='address' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Nearest_landmark</label>
                    <input type='text' class='form-control' id='nearest_landmark' name='nearest_landmark' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Issue</label>
                    <input type='text' class='form-control' id='issue' name='issue' required>
                </div>

                <div class='form-group'>
                    <label for='name'>Troubleshooting1</label>
                    <input type='text' class='form-control' id='troubleshooting1' name='troubleshooting1' required>
                </div>

                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
