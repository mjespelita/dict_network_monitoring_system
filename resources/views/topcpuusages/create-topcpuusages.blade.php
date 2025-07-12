
@extends('layouts.main')

@section('content')
    <h1>Create a new topcpuusages</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('topcpuusages.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Mac</label>
            <input type='text' class='form-control' id='mac' name='mac' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>CpuUtil</label>
            <input type='text' class='form-control' id='cpuUtil' name='cpuUtil' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Model</label>
            <input type='text' class='form-control' id='model' name='model' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ModelVersion</label>
            <input type='text' class='form-control' id='modelVersion' name='modelVersion' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Type</label>
            <input type='text' class='form-control' id='type' name='type' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
