
@extends('layouts.main')

@section('content')
    <h1>Edit Restorations</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('restorations.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' value='{{ $item->siteId }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Time</label>
            <input type='text' class='form-control' id='time' name='time' value='{{ $item->time }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Troubleshoot</label>
            <input type='text' class='form-control' id='troubleshoot' name='troubleshoot' value='{{ $item->troubleshoot }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
