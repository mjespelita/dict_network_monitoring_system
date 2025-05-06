
@extends('layouts.main')

@section('content')
    <h1>Customers Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>ID</th>
                        <td>{{ $item->id }}</td>
                    </tr>
                    
        <tr>
            <th>CustomerId</th>
            <td>{{ $item->customerId }}</td>
        </tr>
    
        <tr>
            <th>Name</th>
            <td>{{ $item->name }}</td>
        </tr>
    
        <tr>
            <th>Description</th>
            <td>{{ empty($item->description) ? "no description" : $item->description }}</td>
        </tr>
    
        {{-- <tr>
            <th>Uploaded By</th>
            <td>{{ $item->users->name ?? "no data" }}</td>
        </tr> --}}  
    
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

    <a href='{{ route('customers.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
