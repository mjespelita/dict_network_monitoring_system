
@extends('layouts.main')

@section('content')
    <h1>Edit Auditlogs</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('auditlogs.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Time</label>
            <input type='text' class='form-control' id='time' name='time' value='{{ $item->time }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Operator</label>
            <input type='text' class='form-control' id='operator' name='operator' value='{{ $item->operator }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Resource</label>
            <input type='text' class='form-control' id='resource' name='resource' value='{{ $item->resource }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Ip</label>
            <input type='text' class='form-control' id='ip' name='ip' value='{{ $item->ip }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>AuditType</label>
            <input type='text' class='form-control' id='auditType' name='auditType' value='{{ $item->auditType }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Level</label>
            <input type='text' class='form-control' id='level' name='level' value='{{ $item->level }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Result</label>
            <input type='text' class='form-control' id='result' name='result' value='{{ $item->result }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Content</label>
            <input type='text' class='form-control' id='content' name='content' value='{{ $item->content }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Label</label>
            <input type='text' class='form-control' id='label' name='label' value='{{ $item->label }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>OldValue</label>
            <input type='text' class='form-control' id='oldValue' name='oldValue' value='{{ $item->oldValue }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NewValue</label>
            <input type='text' class='form-control' id='newValue' name='newValue' value='{{ $item->newValue }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
