
@extends('layouts.main')

@section('content')
    <h1>Create a new auditlogs</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('auditlogs.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Time</label>
            <input type='text' class='form-control' id='time' name='time' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Operator</label>
            <input type='text' class='form-control' id='operator' name='operator' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Resource</label>
            <input type='text' class='form-control' id='resource' name='resource' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Ip</label>
            <input type='text' class='form-control' id='ip' name='ip' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>AuditType</label>
            <input type='text' class='form-control' id='auditType' name='auditType' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Level</label>
            <input type='text' class='form-control' id='level' name='level' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Result</label>
            <input type='text' class='form-control' id='result' name='result' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Content</label>
            <input type='text' class='form-control' id='content' name='content' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Label</label>
            <input type='text' class='form-control' id='label' name='label' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>OldValue</label>
            <input type='text' class='form-control' id='oldValue' name='oldValue' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NewValue</label>
            <input type='text' class='form-control' id='newValue' name='newValue' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
