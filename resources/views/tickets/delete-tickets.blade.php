
@extends('layouts.main')

@section('content')
    <h1>Are you sure you want to delete this tickets?</h1>

    <form action='{{ route('tickets.destroy', $item->id) }}' method='GET'>
        @csrf
        @method('DELETE')
        <button type='submit' class='btn btn-danger'>Yes, Delete</button>
        <a href='{{ route('tickets.index') }}' class='btn btn-secondary'>Cancel</a>
    </form>
@endsection
