
@extends('layouts.main')

@section('content')
    <h1>Auditlogs Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>Time</th>
            <td>{{ $item->time }}</td>
        </tr>
    
        <tr>
            <th>Operator</th>
            <td>{{ $item->operator }}</td>
        </tr>
    
        <tr>
            <th>Resource</th>
            <td>{{ $item->resource }}</td>
        </tr>
    
        <tr>
            <th>Ip</th>
            <td>{{ $item->ip }}</td>
        </tr>
    
        <tr>
            <th>AuditType</th>
            <td>{{ $item->auditType }}</td>
        </tr>
    
        <tr>
            <th>Level</th>
            <td>{{ $item->level }}</td>
        </tr>
    
        <tr>
            <th>Result</th>
            <td>{{ $item->result }}</td>
        </tr>
    
        <tr>
            <th>Content</th>
            <td>{{ $item->content }}</td>
        </tr>
    
        <tr>
            <th>Label</th>
            <td>{{ $item->label }}</td>
        </tr>
    
        <tr>
            <th>OldValue</th>
            <td>{{ $item->oldValue }}</td>
        </tr>
    
        <tr>
            <th>NewValue</th>
            <td>{{ $item->newValue }}</td>
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

    <a href='{{ route('auditlogs.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
