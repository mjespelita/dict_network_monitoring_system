
@extends('layouts.main')

@section('content')
    <h1>Are you sure you want to delete this restorations?</h1>

    <form action='{{ route('restorations.destroy', $item->id) }}' method='GET'>
        @csrf
        @method('DELETE')
        <button type='submit' class='btn btn-danger'>Yes, Delete</button>
        <a href='{{ route('restorations.index') }}' class='btn btn-secondary'>Cancel</a>
    </form>
@endsection
