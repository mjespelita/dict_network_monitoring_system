
@extends('layouts.main')

@section('content')
    <h1>Edit Customers</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('customers.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>CustomerId</label>
            <input type='text' class='form-control' id='customerId' name='customerId' value='{{ $item->customerId }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Description</label>
            <input type='text' class='form-control' id='description' name='description' value='{{ $item->description }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Users_id</label>
            <input type='text' class='form-control' id='users_id' name='users_id' value='{{ $item->users_id }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
