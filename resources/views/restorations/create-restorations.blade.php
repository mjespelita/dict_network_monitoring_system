
@extends('layouts.main')

@section('content')
    <h1>Create a new restorations</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('restorations.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Time</label>
            <input type='text' class='form-control' id='time' name='time' required>
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
