
@extends('layouts.main')

@section('content')
    <h1>Edit User Account</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('useraccounts.update', $item->id) }}' method='POST'>
                @csrf

        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>

        <div class='form-group'>
            <label for='name'>Email</label>
            <input type='text' class='form-control' id='email' name='email' value='{{ $item->email }}' required>
        </div>

        <div class='form-group'>
            <label for='name'>Password</label>
            <input type='password' class='form-control' id='password' name='password' value='{{ $item->password }}' required>
        </div>

        <div class='form-group'>
            <label for='name'>Role</label>
            <select name="role" class="form-control" id="" required>
                <option value="admin">Admin</option>
                <option value="admin">User</option>
            </select>
            {{-- <input type='text' class='form-control' id='role' name='role' value='{{ $item->role }}' required> --}}
        </div>

                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
