
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>All Overviewdiagrams</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            <a href='{{ url('trash-overviewdiagrams') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Overviewdiagrams::where('isTrash', '1')->count() }}</span></button></a>
            <a href='{{ route('overviewdiagrams.create') }}'><button class='btn btn-success'><i class='fas fa-plus'></i> Add Overviewdiagrams</button></a>
        </div>
    </div>
    
    <div class='card'>
        <div class='card-body'>
            <div class='row'>
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
                            <form action='{{ url('/overviewdiagrams-paginate') }}' method='get'>
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
                    <form action='{{ url('/overviewdiagrams-filter') }}' method='get'>
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
                    <form action='{{ url('/overviewdiagrams-search') }}' method='GET'>
                        <div class='input-group'>
                            <input type='text' name='search' value='{{ request()->get('search') }}' class='form-control' placeholder='Search...'>
                            <div class='input-group-append'>
                                <button class='btn btn-success' type='submit'><i class='fa fa-search'></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class='table-responsive'>
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th scope='col'>
                            <input type='checkbox' name='' id='' class='checkAll'>
                            </th>
                            <th>#</th>
                            <th>TotalGatewayNum</th><th>ConnectedGatewayNum</th><th>DisconnectedGatewayNum</th><th>TotalSwitchNum</th><th>ConnectedSwitchNum</th><th>DisconnectedSwitchNum</th><th>TotalPorts</th><th>AvailablePorts</th><th>PowerConsumption</th><th>TotalApNum</th><th>ConnectedApNum</th><th>IsolatedApNum</th><th>DisconnectedApNum</th><th>TotalClientNum</th><th>WiredClientNum</th><th>WirelessClientNum</th><th>GuestNum</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($overviewdiagrams as $item)
                            <tr>
                                <th scope='row'>
                                    <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                </th>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->totalGatewayNum }}</td><td>{{ $item->connectedGatewayNum }}</td><td>{{ $item->disconnectedGatewayNum }}</td><td>{{ $item->totalSwitchNum }}</td><td>{{ $item->connectedSwitchNum }}</td><td>{{ $item->disconnectedSwitchNum }}</td><td>{{ $item->totalPorts }}</td><td>{{ $item->availablePorts }}</td><td>{{ $item->powerConsumption }}</td><td>{{ $item->totalApNum }}</td><td>{{ $item->connectedApNum }}</td><td>{{ $item->isolatedApNum }}</td><td>{{ $item->disconnectedApNum }}</td><td>{{ $item->totalClientNum }}</td><td>{{ $item->wiredClientNum }}</td><td>{{ $item->wirelessClientNum }}</td><td>{{ $item->guestNum }}</td>
                                <td>
                                    <a href='{{ route('overviewdiagrams.show', $item->id) }}'><i class='fas fa-eye text-success'></i></a>
                                    <a href='{{ route('overviewdiagrams.edit', $item->id) }}'><i class='fas fa-edit text-info'></i></a>
                                    <a href='{{ route('overviewdiagrams.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>No Record...</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $overviewdiagrams->links('pagination::bootstrap-5') }}

    <script src='{{ url('assets/jquery/jquery.min.js') }}'></script>
    <script>
        $(document).ready(function () {

            // checkbox

            var click = false;
            $('.checkAll').on('click', function() {
                $('.check').prop('checked', !click);
                click = !click;
                this.innerHTML = click ? 'Deselect' : 'Select';
            });

            $('.bulk-delete').click(function () {
                let array = [];
                $('.check:checked').each(function() {
                    array.push($(this).attr('data-id'));
                });

                $.post('/overviewdiagrams-delete-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })

            $('.bulk-move-to-trash').click(function () {
                let array = [];
                $('.check:checked').each(function() {
                    array.push($(this).attr('data-id'));
                });

                $.post('/overviewdiagrams-move-to-trash-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })
        });
    </script>
@endsection
