
@extends('layouts.main')

@section('content')
    <h1>Are you sure you want to delete this incidents?</h1>

    <form action='{{ route('incidents.destroy', $item->id) }}' method='GET'>
        @csrf
        @method('DELETE')
        <button type='submit' class='btn btn-danger'>Yes, Delete</button>
        <a href='{{ route('incidents.index') }}' class='btn btn-secondary'>Cancel</a>
    </form>
@endsection
