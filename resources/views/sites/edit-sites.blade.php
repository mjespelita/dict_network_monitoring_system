
@extends('layouts.main')

@section('content')
    <h1>Edit Sites</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('sites.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' value='{{ $item->siteId }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>CustomerId</label>
            <input type='text' class='form-control' id='customerId' name='customerId' value='{{ $item->customerId }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>CustomerName</label>
            <input type='text' class='form-control' id='customerName' name='customerName' value='{{ $item->customerName }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Region</label>
            <input type='text' class='form-control' id='region' name='region' value='{{ $item->region }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Timezone</label>
            <input type='text' class='form-control' id='timezone' name='timezone' value='{{ $item->timezone }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Scenario</label>
            <input type='text' class='form-control' id='scenario' name='scenario' value='{{ $item->scenario }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Wan</label>
            <input type='text' class='form-control' id='wan' name='wan' value='{{ $item->wan }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedApNum</label>
            <input type='text' class='form-control' id='connectedApNum' name='connectedApNum' value='{{ $item->connectedApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedApNum</label>
            <input type='text' class='form-control' id='disconnectedApNum' name='disconnectedApNum' value='{{ $item->disconnectedApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>IsolatedApNum</label>
            <input type='text' class='form-control' id='isolatedApNum' name='isolatedApNum' value='{{ $item->isolatedApNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedSwitchNum</label>
            <input type='text' class='form-control' id='connectedSwitchNum' name='connectedSwitchNum' value='{{ $item->connectedSwitchNum }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedSwitchNum</label>
            <input type='text' class='form-control' id='disconnectedSwitchNum' name='disconnectedSwitchNum' value='{{ $item->disconnectedSwitchNum }}' required>
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
