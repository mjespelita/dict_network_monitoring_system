
@extends('layouts.main')

@section('content')
    <h1>Create a new clientstats</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('clientstats.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Total</label>
            <input type='text' class='form-control' id='total' name='total' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Wireless</label>
            <input type='text' class='form-control' id='wireless' name='wireless' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Wired</label>
            <input type='text' class='form-control' id='wired' name='wired' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num2g</label>
            <input type='text' class='form-control' id='num2g' name='num2g' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num5g</label>
            <input type='text' class='form-control' id='num5g' name='num5g' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num6g</label>
            <input type='text' class='form-control' id='num6g' name='num6g' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumUser</label>
            <input type='text' class='form-control' id='numUser' name='numUser' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumGuest</label>
            <input type='text' class='form-control' id='numGuest' name='numGuest' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumWirelessUser</label>
            <input type='text' class='form-control' id='numWirelessUser' name='numWirelessUser' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumWirelessGuest</label>
            <input type='text' class='form-control' id='numWirelessGuest' name='numWirelessGuest' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num2gUser</label>
            <input type='text' class='form-control' id='num2gUser' name='num2gUser' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num5gUser</label>
            <input type='text' class='form-control' id='num5gUser' name='num5gUser' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num6gUser</label>
            <input type='text' class='form-control' id='num6gUser' name='num6gUser' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num2gGuest</label>
            <input type='text' class='form-control' id='num2gGuest' name='num2gGuest' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num5gGuest</label>
            <input type='text' class='form-control' id='num5gGuest' name='num5gGuest' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num6gGuest</label>
            <input type='text' class='form-control' id='num6gGuest' name='num6gGuest' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Poor</label>
            <input type='text' class='form-control' id='poor' name='poor' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Fair</label>
            <input type='text' class='form-control' id='fair' name='fair' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NoData</label>
            <input type='text' class='form-control' id='noData' name='noData' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Good</label>
            <input type='text' class='form-control' id='good' name='good' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
