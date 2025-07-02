
@extends('layouts.main')

@section('content')

    <x-internal-sidebar :item="$item" />

    <div class='card'>
        <div class='card-body'>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                Create a Ticket
            </button>

            <a href="{{ url('/ticket-audits') }}">View Ticket Audit Logs</a>

            <!-- Modal -->
            <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createTicketModalLabel">Create A Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action='{{ route('tickets.store') }}' method='POST'>
                                @csrf

                                <div class='form-group'>
                                    {{-- <label for='name'>Site</label> --}}
                                    <input type='text' class='form-control' id='sites_id' value="{{ $item->siteId }}" hidden name='sites_id' required>
                                </div>

                                @php
                                    $dateReported = date('Y-m-d');
                                @endphp

                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        Ticket Information
                                    </div>
                                    <div class="card-body">
                                        <b class="card-title mb-2">Site Name: {{ $item->name }}</b> <br>
                                        <b class="card-title">Date Reported: {{ Smark\Smark\Dater::humanReadableDateWithDay($dateReported) }}</b>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    {{-- <label for='name'>Date_reported</label> --}}
                                    <input type='date' class='form-control' hidden id='date_reported' value="{{ $dateReported }}" name='date_reported' required>
                                </div>

                                <div class='form-group'>
                                    {{-- <label for='name'>Name</label> --}}
                                    <input type='text' class='form-control' hidden value="{{ $item->name }}" id='name' name='name' required>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Ticket Type</label>
                                    <select class="form-control" name="ticket_type">
                                        <option value="ir">Incident Response</option>
                                        <option value="sr">Service Request</option>
                                        <option value="cr">Change Request</option>
                                    </select>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Address</label>
                                    <input type='text' class='form-control' id='address' name='address' required>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Nearest Landmark</label>
                                    <input type='text' class='form-control' id='nearest_landmark' name='nearest_landmark' required>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Issue</label>
                                    <textarea name='issue' class="form-control" id="" cols="30" rows="5" placeholder="Issue..." required>No internet</textarea>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Troubleshooting</label>
                                    <textarea name='troubleshooting' class="form-control" id="" cols="30" rows="5" placeholder="Solution to this issue..." required></textarea>
                                </div>

                                <button type='submit' class='btn btn-primary mt-3'>Create</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- For Edit Modal -->
            <div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" action='' method='POST'>
                                @csrf

                                <div class='form-group'>
                                    {{-- <label for='name'>Site</label> --}}
                                    <input type='text' class='form-control' id='sites_id' value="" hidden  name='sites_id' required>
                                </div>

                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        Ticket Information
                                    </div>
                                    <div class="card-body">
                                        <b class="card-title mb-2">Site Name: {{ $item->name }}</b> <br>
                                        <b class="card-title">Date Reported: {{ Smark\Smark\Dater::humanReadableDateWithDay($dateReported) }}</b>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    {{-- <label for='name'>Date_reported</label> --}}
                                    <input type='date' class='form-control' hidden id='date_reported' value="" name='date_reported' required>
                                </div>

                                <div class='form-group'>
                                    {{-- <label for='name'>Name</label> --}}
                                    <input type='text' class='form-control' hidden value="" id='name' name='name' required>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Ticket Type</label>
                                    <select class="form-control" name="ticket_type">
                                        <option value="ir">Incident Response</option>
                                        <option value="sr">Service Request</option>
                                        <option value="cr">Change Request</option>
                                    </select>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Address</label>
                                    <input type='text' class='form-control' id='address' name='address' required>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Nearest Landmark</label>
                                    <input type='text' class='form-control' id='nearest_landmark' name='nearest_landmark' required>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Issue</label>
                                    <textarea name='issue' class="form-control" id="" cols="30" rows="5" placeholder="Issue..." required>No internet</textarea>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Troubleshooting</label>
                                    <textarea name='troubleshooting' class="form-control" id="" cols="30" rows="5" placeholder="Solution to this issue..." required></textarea>
                                </div>

                                <div class='form-group'>
                                    <label for='name'>Status</label>
                                    <select name="status" id="" class="form-control">
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>

                                <button type='submit' class='btn btn-primary mt-3'>Update</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='table-responsive mt-5'>
                <style>
                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                    </style>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Ticket Type</th>
                            <th>Date Reported</th>
                            <th>Site Name</th>
                            <th>Address</th>
                            <th>Nearest Landmark</th>
                            <th>Issue</th>
                            <th>Troubleshooting</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsBody">
                        <!-- tickets will be inserted here -->
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 50px 0;">
                                <div style="display: inline-block; width: 3rem; height: 3rem; border: 0.4rem solid #ccc; border-top-color: #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <div style="margin-top: 1rem; color: #888;">Loading site tickets...</div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            const rowsPerPage = 10;
            let currentPage = 1;
            let logsData = [];

            function renderTablePage(page) {
                const container = $('#ticketsBody');
                container.empty();

                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = logsData.slice(start, end);

                if (pageData.length === 0) {
                    container.html(`<tr><td colspan="2" class="text-center text-muted">No log data.</td></tr>`);
                    return;
                }

                pageData.forEach(ticket => {

                    if (ticket.ticket_type === 'IR') {
                        ticket.ticket_type = 'Incident Response'
                    }

                    if (ticket.ticket_type === 'SR') {
                        ticket.ticket_type = 'Service Request'
                    }

                    if (ticket.ticket_type === 'CR') {
                        ticket.ticket_type = 'Change Request'
                    }

                    const row = `
                        <tr>
                            <td>${ticket.ticket_number}</td>
                            <td>${ticket.ticket_type}</td>
                            <td>${ticket.date_reported}</td>
                            <td>${ticket.name}</td>
                            <td>${ticket.address}</td>
                            <td>${ticket.nearest_landmark}</td>
                            <td>${ticket.issue}</td>
                            <td>${ticket.troubleshooting}</td>
                            <td>${ticket.status}</td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-primary edit-ticket-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTicketModal"
                                    data-ticket='${JSON.stringify(ticket)}'>
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-primary delete-ticket-btn" data-ticket='${JSON.stringify(ticket)}'>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    container.append(row);
                });
            }

            $(document).on('click', '.edit-ticket-btn', function () {
                const ticket = $(this).data('ticket');

                $('#editForm').attr('action', '/update-tickets/' + ticket.id);
                $('input[name="sites_id"]').val(ticket.sites_id);
                $('input[name="ticket_number"]').val(ticket.ticket_number);
                $('input[name="date_reported"]').val(ticket.date_reported);
                $('input[name="name"]').val(ticket.name);
                $('input[name="address"]').val(ticket.address);
                $('input[name="nearest_landmark"]').val(ticket.nearest_landmark);
                $('textarea[name="issue"]').val(ticket.issue);
                $('textarea[name="troubleshooting"]').val(ticket.troubleshooting);

                console.log('Populated edit modal with ticket:', ticket);
            });

            $(document).on('click', '.delete-ticket-btn', function () {
                const ticket = $(this).data('ticket');

                Swal.fire({
                    title: "Do you want to delete this ticket?",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    denyButtonText: `No`
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {

                        $.get('/destroy-tickets/' + ticket.id, function (res) {
                            Swal.fire("Deleted!", "", "success");
                            window.location.reload()
                        }).fail(err => {
                            Swal.fire("Something Went Wrong", "", "info");
                        });

                    } else if (result.isDenied) {
                        Swal.fire("Deletion cancelled", "", "info");
                    }
                });

                console.log('Populated edit modal with ticket:', ticket.id);
            });


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

            function ticketsApi(siteId) {
                $.ajax({
                    url: `/tickets-api/${siteId}`,
                    method: 'GET',
                    success: function (res) {
                        if (res) {
                            logsData = res;
                            currentPage = 1;
                            renderTablePage(currentPage);
                            renderPagination(logsData.length);
                        } else {
                            $.get('/generate-new-api-token', function (res) {
                                window.location.reload();
                            });
                            $('#ticketsBody').html(`<tr><td colspan="2" class="text-danger text-center">Failed to load logs.</td></tr>`);
                        }
                    },
                    error: function () {
                        $('#ticketsBody').html(`<tr><td colspan="2" class="text-danger text-center">Request failed.</td></tr>`);
                    }
                });
            }

            // Initial load
            $.get('/traffic-api-access-token', function () {
                const siteId = window.location.pathname.split('/').filter(Boolean).pop();
                ticketsApi(siteId);
            }).fail(function () {
                console.error("Failed to get access token.");
            });
        });
        </script>
@endsection
