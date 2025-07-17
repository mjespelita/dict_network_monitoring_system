
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>All Disconnected Devices</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            {{-- <a href='{{ url('trash-disconnecteddevices') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Disconnecteddevices::where('isTrash', '1')->count() }}</span></button></a>
            <a href='{{ route('disconnecteddevices.create') }}'><button class='btn btn-success'><i class='fas fa-plus'></i> Add Disconnecteddevices</button></a> --}}
        </div>
    </div>

    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='mt-2 col-lg-4 col-md-4 col-sm-12'>
                    <div class='row'>
                        <div class='col-4'>
                            {{-- <button type='button' class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                Action
                            </button>
                            <div class='dropdown-menu'>
                                <a class='dropdown-item bulk-move-to-trash' href='#'>
                                    <i class='fa fa-trash'></i> Move to Trash
                                </a>
                                <a class='dropdown-item bulk-delete' href='#'>
                                    <i class='fa fa-trash'></i> <span class='text-danger'>Delete Permanently</span> <br> <small>(this action cannot be undone)</small>
                                </a>
                            </div> --}}
                        </div>
                        <div class='col-8'>
                            <form action='{{ url('/disconnecteddevices-paginate') }}' method='get'>
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
                <div class='mt-2 col-lg-4 col-md-4 col-sm-12'>
                    <form action='{{ url('/disconnecteddevices-filter') }}' method='get'>
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
                <div class='mt-2 col-lg-4 col-md-4 col-sm-12'>
                    <!-- Search Form -->
                    <form action='{{ url('/disconnecteddevices-search') }}' method='GET'>
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
                            <th>Status</th>
                            <th>Reported</th>
                            <th>Name</th>
                            <th>Device Name</th>
                            <th>Device Mac</th>
                            <th>Device Type</th>
                            <th>Detected At</th>
                            {{-- <th></th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($disconnecteddevices as $item)
                            <tr>
                                <td class="fw-bold text-danger">
                                    <i class="fas fa-plug text-danger"></i> DISCONNECTED
                                </td>
                                <td class="fw-bold {{ $item->isReported ? 'text-success' : 'text-danger' }}">
                                    {{ $item->isReported ? 'Yes' : 'No' }}
                                </td>
                                <td class="fw-bold text-primary">
                                    <a href="{{ url('show-sites/'.$item->siteId) }}" class="nav-link">{{ $item->name }}</a>
                                </td class="fw-bold text-danger">
                                <td class="fw-bold text-danger">
                                    {{ $item->device_name }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ $item->device_mac }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ $item->device_type }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->created_at) }}
                                </td>
                                <td>
                                    {{-- <a href='{{ route('disconnecteddevices.show', $item->id) }}'><i class='fas fa-eye text-success'></i></a> --}}
                                    <a class="nav-link" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#reportDisconnectedDevice{{ $item->siteId }}"><i class='fas fa-share text-info'></i> Submit A Report</a>
                                    <a class="nav-link" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#restoreDisconnectedDevice{{ $item->siteId }}"><i class='fas fa-recycle text-success'></i> Submit A Restore Report</a>
                                    {{-- <a href='{{ route('disconnecteddevices.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a> --}}

                                    <!-- Modal -->
                                    <div class="modal fade" id="restoreDisconnectedDevice{{ $item->siteId }}" tabindex="-1" aria-labelledby="offlineSiteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                            <div class="text-white modal-header bg-danger">
                                                <h5 class="modal-title" id="offlineSiteModalLabel">🚨 Restore - {{ $item->device_name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('restore-disconnected-device/'.$item->siteId) }}" method="post">
                                                    <div class="form-group">
                                                        <label for="">Ticket Number</label>
                                                        <input type="text" class="form-control" name="ticket_number">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Reason</label>
                                                        <input type="text" class="form-control" name="reason">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Troubleshooting</label>
                                                        <textarea name="troubleshoot" class="form-control" id="" cols="30" rows="10"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="form-control bg-danger text-light">Mark As Restored</button>
                                                    </div>
                                                    @csrf
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="reportDisconnectedDevice{{ $item->siteId }}" tabindex="-1" aria-labelledby="offlineSiteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                            <div class="text-white modal-header bg-danger">
                                                <h5 class="modal-title" id="offlineSiteModalLabel">🚨 Submit A Report - {{ $item->device_name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('report-disconnected-device/'.$item->siteId) }}" method="post">
                                                    <div class="form-group">
                                                        <label for="">Reason</label>
                                                        <input type="text" class="form-control" name="reason">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Troubleshooting</label>
                                                        <textarea name="troubleshoot" class="form-control" id="" cols="30" rows="10"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="form-control bg-danger text-light">Submit A Report</button>
                                                    </div>
                                                    @csrf
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

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

    {{ $disconnecteddevices->links('pagination::bootstrap-5') }}

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

                $.post('/disconnecteddevices-delete-all-bulk-data', {
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

                $.post('/disconnecteddevices-move-to-trash-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })
        });
    </script>
@endsection
