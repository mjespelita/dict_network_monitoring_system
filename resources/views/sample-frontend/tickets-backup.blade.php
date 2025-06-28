
@extends('layouts.main')

@section('content')

    <x-internal-sidebar :item="$item" />

    <div class='card'>
        <div class='card-body'>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                Create a Ticket
            </button>

            <!-- Modal -->
            <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
            <div class="modal-diaticket">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">Create A Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    adasd
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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
                            <th>Date Reported</th>
                            <th>Site Name</th>
                            <th>Address</th>
                            <th>Nearest Landmark</th>
                            <th>Issue</th>
                            <th>Troubleshooting</th>
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
                    <ul class="pagination justify-content-center" id="ticketPagination"></ul>
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
            let ticketsData = [];

            function renderTablePage(page) {
                const container = $('#ticketsBody');
                container.empty();

                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = ticketsData.slice(start, end);

                if (pageData.length === 0) {
                    container.html(`<tr><td colspan="2" class="text-center text-muted">No ticket data.</td></tr>`);
                    return;
                }

                pageData.forEach(ticket => {
                    const row = `
                        <tr>
                            <td>${ticket.ticket_number}</td>
                            <td>${ticket.date_reported}</td>
                            <td>${ticket.name}</td>
                            <td>${ticket.address}</td>
                            <td>${ticket.nearest_landmark}</td>
                            <td>${ticket.issue}</td>
                            <td>${ticket.troubleshooting}</td>
                        </tr>
                    `;
                    container.append(row);
                });
            }

            function renderPagination(totalItems) {
                const pageCount = Math.ceil(totalItems / rowsPerPage);
                const pagination = $('#ticketPagination');
                pagination.empty();

                for (let i = 1; i <= pageCount; i++) {
                    const li = $(`<li class="page-item ${i === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#">${i}</a>
                                  </li>`);
                    li.click(function (e) {
                        e.preventDefault();
                        currentPage = i;
                        renderTablePage(currentPage);
                        renderPagination(ticketsData.length);
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
                            ticketsData = res;
                            currentPage = 1;
                            renderTablePage(currentPage);
                            renderPagination(ticketsData.length);
                        } else {
                            $.get('/generate-new-api-token', function (res) {
                                window.location.reload();
                            });
                            $('#ticketsBody').html(`<tr><td colspan="2" class="text-danger text-center">Failed to load tickets.</td></tr>`);
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
