
@extends('layouts.main')

@section('content')
    <h1>Create a new uploads</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('uploads.store') }}' method='POST' enctype="multipart/form-data">
                @csrf
                
        <div class='form-group my-4'>
            <label for='name'>Customers</label>
            <input type='file' class='form-control' id='file' name='customers' required>
        </div>

        <div class='form-group my-4'>
            <label for='name'>Sites</label>
            <input type='file' class='form-control' id='file' name='sites' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
