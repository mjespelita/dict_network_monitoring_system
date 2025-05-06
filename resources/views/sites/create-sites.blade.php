
@extends('layouts.main')

@section('content')
    <h1>Create a new sites</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('sites.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>SiteId</label>
            <input type='text' class='form-control' id='siteId' name='siteId' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>CustomerId</label>
            <input type='text' class='form-control' id='customerId' name='customerId' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>CustomerName</label>
            <input type='text' class='form-control' id='customerName' name='customerName' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Region</label>
            <input type='text' class='form-control' id='region' name='region' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Timezone</label>
            <input type='text' class='form-control' id='timezone' name='timezone' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Scenario</label>
            <input type='text' class='form-control' id='scenario' name='scenario' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Wan</label>
            <input type='text' class='form-control' id='wan' name='wan' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedApNum</label>
            <input type='text' class='form-control' id='connectedApNum' name='connectedApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedApNum</label>
            <input type='text' class='form-control' id='disconnectedApNum' name='disconnectedApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>IsolatedApNum</label>
            <input type='text' class='form-control' id='isolatedApNum' name='isolatedApNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>ConnectedSwitchNum</label>
            <input type='text' class='form-control' id='connectedSwitchNum' name='connectedSwitchNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>DisconnectedSwitchNum</label>
            <input type='text' class='form-control' id='disconnectedSwitchNum' name='disconnectedSwitchNum' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Type</label>
            <input type='text' class='form-control' id='type' name='type' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
