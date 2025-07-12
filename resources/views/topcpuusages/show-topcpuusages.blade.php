
@extends('layouts.main')

@section('content')
    <h1>Topcpuusages Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Name</th>
            <td>{{ $item->name }}</td>
        </tr>
    
        <tr>
            <th>Mac</th>
            <td>{{ $item->mac }}</td>
        </tr>
    
        <tr>
            <th>CpuUtil</th>
            <td>{{ $item->cpuUtil }}</td>
        </tr>
    
        <tr>
            <th>Model</th>
            <td>{{ $item->model }}</td>
        </tr>
    
        <tr>
            <th>ModelVersion</th>
            <td>{{ $item->modelVersion }}</td>
        </tr>
    
        <tr>
            <th>Type</th>
            <td>{{ $item->type }}</td>
        </tr>
    
                    <tr>
                        <th>Created At</th>
                        <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->updated_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <a href='{{ route('topcpuusages.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
