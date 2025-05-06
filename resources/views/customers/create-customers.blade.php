
@extends('layouts.main')

@section('content')
    <h1>Create a new customers</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('customers.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>CustomerId</label>
            <input type='text' class='form-control' id='customerId' name='customerId' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Description</label>
            <input type='text' class='form-control' id='description' name='description' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Users_id</label>
            <input type='text' class='form-control' id='users_id' name='users_id' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
