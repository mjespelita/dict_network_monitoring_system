
@extends('layouts.main')

@section('content')
    <h1>Edit Clientstats</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('clientstats.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Total</label>
            <input type='text' class='form-control' id='total' name='total' value='{{ $item->total }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Wireless</label>
            <input type='text' class='form-control' id='wireless' name='wireless' value='{{ $item->wireless }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Wired</label>
            <input type='text' class='form-control' id='wired' name='wired' value='{{ $item->wired }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num2g</label>
            <input type='text' class='form-control' id='num2g' name='num2g' value='{{ $item->num2g }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num5g</label>
            <input type='text' class='form-control' id='num5g' name='num5g' value='{{ $item->num5g }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num6g</label>
            <input type='text' class='form-control' id='num6g' name='num6g' value='{{ $item->num6g }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumUser</label>
            <input type='text' class='form-control' id='numUser' name='numUser' value='{{ $item->numUser }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumGuest</label>
            <input type='text' class='form-control' id='numGuest' name='numGuest' value='{{ $item->numGuest }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumWirelessUser</label>
            <input type='text' class='form-control' id='numWirelessUser' name='numWirelessUser' value='{{ $item->numWirelessUser }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NumWirelessGuest</label>
            <input type='text' class='form-control' id='numWirelessGuest' name='numWirelessGuest' value='{{ $item->numWirelessGuest }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num2gUser</label>
            <input type='text' class='form-control' id='num2gUser' name='num2gUser' value='{{ $item->num2gUser }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num5gUser</label>
            <input type='text' class='form-control' id='num5gUser' name='num5gUser' value='{{ $item->num5gUser }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num6gUser</label>
            <input type='text' class='form-control' id='num6gUser' name='num6gUser' value='{{ $item->num6gUser }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num2gGuest</label>
            <input type='text' class='form-control' id='num2gGuest' name='num2gGuest' value='{{ $item->num2gGuest }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num5gGuest</label>
            <input type='text' class='form-control' id='num5gGuest' name='num5gGuest' value='{{ $item->num5gGuest }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Num6gGuest</label>
            <input type='text' class='form-control' id='num6gGuest' name='num6gGuest' value='{{ $item->num6gGuest }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Poor</label>
            <input type='text' class='form-control' id='poor' name='poor' value='{{ $item->poor }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Fair</label>
            <input type='text' class='form-control' id='fair' name='fair' value='{{ $item->fair }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>NoData</label>
            <input type='text' class='form-control' id='noData' name='noData' value='{{ $item->noData }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Good</label>
            <input type='text' class='form-control' id='good' name='good' value='{{ $item->good }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' value='{{ $item->siteId }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
