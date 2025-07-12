
@extends('layouts.main')

@section('content')
    <h1>Edit Topcpuusages</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('topcpuusages.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Mac</label>
            <input type='text' class='form-control' id='mac' name='mac' value='{{ $item->mac }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>CpuUtil</label>
            <input type='text' class='form-control' id='cpuUtil' name='cpuUtil' value='{{ $item->cpuUtil }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Model</label>
            <input type='text' class='form-control' id='model' name='model' value='{{ $item->model }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ModelVersion</label>
            <input type='text' class='form-control' id='modelVersion' name='modelVersion' value='{{ $item->modelVersion }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Type</label>
            <input type='text' class='form-control' id='type' name='type' value='{{ $item->type }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
