
@extends('layouts.main')

@section('content')
    <h1>Create a new batches</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('batches.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Batch_number</label>
            <input type='text' class='form-control' id='batch_number' name='batch_number' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
