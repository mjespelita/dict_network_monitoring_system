
@extends('layouts.main')

@section('content')
    <h1>{{ $item->name }}</h1>

    <x-internal-sidebar :item="$item" />

    <div class="card">
        <div class="card-body">

            {{-- <div class='row'>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <div class='row'>
                        <div class='col-4'>
                            <button type='button' class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                Action
                            </button>
                            <div class='dropdown-menu'>
                                <a class='dropdown-item bulk-move-to-trash' href='#'>
                                    <i class='fa fa-trash'></i> Move to Trash
                                </a>
                                <a class='dropdown-item bulk-delete' href='#'>
                                    <i class='fa fa-trash'></i> <span class='text-danger'>Delete Permanently</span> <br> <small>(this action cannot be undone)</small>
                                </a>
                            </div>
                        </div>
                        <div class='col-8'>
                            <form action='{{ url('/sites-paginate') }}' method='get'>
                                <div class='input-group'>
                                    <input type='number' name='paginate' class='form-control' placeholder='Paginate' value='{{ request()->get('paginate', 10) }}'>
                                    <div class='input-group-append'>
                                        <button class='btn btn-success' type='submit'><i class='fa fa-bars'></i></button>
                                    </div>
                                </div>
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <form action='{{ url('/sites-filter') }}' method='get'>
                        <div class='input-group'>
                            <input type='date' class='form-control' id='from' name='from' required>
                            <b class='pt-2'>- to -</b>
                            <input type='date' class='form-control' id='to' name='to' required>
                            <div class='input-group-append'>
                                <button type='submit' class='btn btn-primary form-control'><i class='fas fa-filter'></i></button>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <!-- Search Form -->
                    <form action='{{ url('/sites-search') }}' method='GET'>
                        <div class='input-group'>
                            <input type='text' name='search' value='{{ request()->get('search') }}' class='form-control' placeholder='Search...'>
                            <div class='input-group-append'>
                                <button class='btn btn-success' type='submit'><i class='fa fa-search'></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> --}}

            <style>
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
                </style>

            <div>
                <div class="row" id="overviewDiagramSummary">
                    <div class="loadingPlaceholder" style="width: 100%; text-align: center; padding: 50px 0;">
                        <div style="display: inline-block; width: 3rem; height: 3rem; border: 0.4rem solid #ccc; border-top-color: #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <div style="margin-top: 1rem; color: #888;">Loading overview diagram...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='card'>
        <div class='card-body'>

            <div class='table-responsive'>
                <table class='table'>

                <tr>
                    <th>Name</th>
                    <td>{{ $item->name }}</td>
                </tr>

                <tr>
                    <th>Site Id</th>
                    <td>{{ $item->siteId }}</td>
                </tr>

                <tr>
                    <th>Region</th>
                    <td>{{ $item->region }}</td>
                </tr>

                <tr>
                    <th>Timezone</th>
                    <td>{{ $item->timezone }}</td>
                </tr>

                <tr>
                    <th>Scenario</th>
                    <td>{{ $item->scenario }}</td>
                </tr>

                <tr>
                    <th>Type</th>
                    <td>{{ $item->type }}</td>
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

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>

    <script src='{{ url('assets/jquery/jquery.min.js') }}'></script>
    <script>
        const siteId = window.location.pathname.split('/').filter(Boolean).pop();

        $.get(`/overview-diagram-api/${siteId}`, function(stat) {

            console.log(stat)

            const statCards = `
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Total Clients:</strong> <h1>${stat[0].totalClientNum}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Wired Clients:</strong> <h1>${stat[0].wiredClientNum}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Wireless Clients:</strong> <h1>${stat[0].wirelessClientNum}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Guest Clients:</strong> <h1>${stat[0].guestNum}</h1></div></div>

                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Total APs:</strong> <h1>${stat[0].totalApNum}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Connected APs:</strong> <h1>${stat[0].connectedApNum}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Disconnected APs:</strong> <h1>${stat[0].disconnectedApNum}</h1></div></div>

                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Total Switches:</strong> <h1>${stat[0].totalSwitchNum}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Connected Switches:</strong> <h1>${stat[0].connectedSwitchNum}</h1></div></div>

                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Total Ports:</strong> <h1>${stat[0].totalPorts}</h1></div></div>
                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Available Ports:</strong> <h1>${stat[0].availablePorts}</h1></div></div>

                <div class="col-md-3 mb-3"><div class="card p-3"><strong>Power Consumption:</strong> <h1>${stat[0].powerConsumption} W</h1></div></div>
            `;

            $('#overviewDiagramSummary').html(statCards);
        }).fail(function(err) {
            console.error("Error loading overview diagram:", err);
            $('#overviewDiagramSummary').html(`<div class="text-danger">Failed to load data.</div>`);
        });
    </script>
@endsection
