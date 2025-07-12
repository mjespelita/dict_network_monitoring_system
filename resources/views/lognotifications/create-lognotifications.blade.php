
@extends('layouts.main')

@section('content')
    <h1>Create a new lognotifications</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('lognotifications.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Key</label>
            <input type='text' class='form-control' id='key' name='key' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ShortMsg</label>
            <input type='text' class='form-control' id='shortMsg' name='shortMsg' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
