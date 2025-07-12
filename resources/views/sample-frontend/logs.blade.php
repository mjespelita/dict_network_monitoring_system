
@extends('layouts.main')

@section('content')
    <h1>Logs - {{ $item->name }}</h1>

    <x-internal-sidebar :item="$item" />

    <div class='card'>
        <div class='card-body'>

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

            <div class='table-responsive mt-5'>
                <style>
                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                    </style>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Content</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="logNotificationBody">
                        <!-- Logs will be inserted here -->
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 50px 0;">
                                <div style="display: inline-block; width: 3rem; height: 3rem; border: 0.4rem solid #ccc; border-top-color: #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <div style="margin-top: 1rem; color: #888;">Loading site logs...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination Controls -->
                <nav>
                    <ul class="pagination justify-content-center" id="logPagination"></ul>
                </nav>
            </div>

        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            const rowsPerPage = 10;
            let currentPage = 1;
            let logsData = [];

            function renderTablePage(page) {
                const container = $('#logNotificationBody');
                container.empty();

                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = logsData.slice(start, end);

                if (pageData.length === 0) {
                    container.html(`<tr><td colspan="2" class="text-center text-muted">No log data.</td></tr>`);
                    return;
                }

                pageData.forEach(log => {
                    const row = `
                        <tr>
                            <td>${log.shortMsg}</td>
                            <td>${new Date().toLocaleString()}</td>
                        </tr>
                    `;
                    container.append(row);
                });
            }

            function renderPagination(totalItems) {
                const pageCount = Math.ceil(totalItems / rowsPerPage);
                const pagination = $('#logPagination');
                pagination.empty();

                for (let i = 1; i <= pageCount; i++) {
                    const li = $(`<li class="page-item ${i === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#">${i}</a>
                                  </li>`);
                    li.click(function (e) {
                        e.preventDefault();
                        currentPage = i;
                        renderTablePage(currentPage);
                        renderPagination(logsData.length);
                    });
                    pagination.append(li);
                }
            }

            function loadLogNotifications(siteId) {
                $.ajax({
                    url: `/log-notification-api/${siteId}`,
                    method: 'GET',
                    success: function (res) {
                        if (res.errorCode === 0 && res.result.logNotifications) {
                            logsData = res.result.logNotifications;
                            currentPage = 1;
                            renderTablePage(currentPage);
                            renderPagination(logsData.length);
                        } else {
                            $('#logNotificationBody').html(`<tr><td colspan="2" class="text-danger text-center">Failed to load logs.</td></tr>`);
                        }
                    },
                    error: function () {
                        $('#logNotificationBody').html(`<tr><td colspan="2" class="text-danger text-center">Request failed.</td></tr>`);
                    }
                });
            }

            const siteId = window.location.pathname.split('/').filter(Boolean).pop();
            loadLogNotifications(siteId);
        });
        </script>

@endsection
