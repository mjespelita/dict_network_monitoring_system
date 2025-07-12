
@extends('layouts.main')

@section('content')
    <h1>Edit Lognotifications</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('lognotifications.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Key</label>
            <input type='text' class='form-control' id='key' name='key' value='{{ $item->key }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ShortMsg</label>
            <input type='text' class='form-control' id='shortMsg' name='shortMsg' value='{{ $item->shortMsg }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' value='{{ $item->siteId }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
