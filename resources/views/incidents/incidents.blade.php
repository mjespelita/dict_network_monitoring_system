
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>Offline</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            {{-- <a href='{{ url('trash-incidents') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Incidents::where('isTrash', '1')->count() }}</span></button></a>
            <a href='{{ route('incidents.create') }}'><button class='btn btn-success'><i class='fas fa-plus'></i> Add Incidents</button></a> --}}
        </div>
    </div>

    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
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
                            <form action='{{ url('/incidents-paginate') }}' method='get'>
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
                    <form action='{{ url('/incidents-filter') }}' method='get'>
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
                    <form action='{{ url('/incidents-search') }}' method='GET'>
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
                            {{-- <th scope='col'>
                            <input type='checkbox' name='' id='' class='checkAll'>
                            </th> --}}
                            <th>Status</th>
                            <th>Name</th>
                            <th>SiteId</th>
                            <th>Reported</th>
                            <th>Detected At</th>
                            <th>Recorded At</th>
                            {{-- <th>Actions</th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($incidents as $item)
                            <tr>
                                {{-- <th scope='row'>
                                    <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                </th> --}}
                                <td class="fw-bold text-danger">
                                    <i class="fas fa-ban text-danger"></i> OFFLINE
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ $item->name }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ $item->siteId }}
                                </td>
                                <td class="fw-bold {{ $item->isReported ? 'text-success' : 'text-danger' }}">
                                    {{ $item->isReported ? 'Yes' : 'No' }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ $item->time }}
                                </td>
                                <td>
                                    {{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->created_at) }}
                                </td>
                                <td>
                                    {{-- <a href='{{ route('incidents.show', $item->id) }}'><i class='fas fa-eye text-success'></i></a> --}}
                                    <a class="nav-link" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#reportOffline{{ $item->siteId }}"><i class='fas fa-share text-info'></i> Submit A Report</a>
                                    <a class="nav-link" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#restoreOffline{{ $item->siteId }}"><i class='fas fa-recycle text-success'></i> Restore</a>
                                    {{-- <a href='{{ route('incidents.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a> --}}

                                    <!-- Modal -->
                                    <div class="modal fade" id="restoreOffline{{ $item->siteId }}" tabindex="-1" aria-labelledby="offlineSiteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="offlineSiteModalLabel">🚨 Restore - {{ $item->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('restore-offline-site/'.$item->siteId) }}" method="post">
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
                                    <div class="modal fade" id="reportOffline{{ $item->siteId }}" tabindex="-1" aria-labelledby="offlineSiteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="offlineSiteModalLabel">🚨 Submit A Report - {{ $item->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('report-offline-site/'.$item->siteId) }}" method="post">
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

    {{ $incidents->links('pagination::bootstrap-5') }}

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

                $.post('/incidents-delete-all-bulk-data', {
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

                $.post('/incidents-move-to-trash-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })
        });
    </script>
@endsection
