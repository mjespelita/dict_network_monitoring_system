
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>All Sites</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            {{-- {{-- <a href='{{ url('trash-sites') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Sites::where('isTrash', '1')->count() }}</span></button></a> --}}
            <button class='btn btn-success' data-bs-toggle="modal" data-bs-target="#exampleModal"><i class='fas fa-file-pdf' ></i> Export</button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Export Report</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 text-start">
                                <small class="text-info"><i>Note: Please fill out the necessary form fields below to proceed with exporting the report.</i></small>
                            </div>
                            <form action="{{ url('/export-general-data-into-pdf') }}" method="GET" class="mb-4 row g-3">
                                <h4 class="text-start">Site Selection</h4>
                                <div class="col-md-12">
                                    <label for="startDate" class="form-label text-start w-100">Select A Site</label>
                                    <select name="site" id="" class="form-control" required>
                                            <option value="all">All Sites</option>
                                        @forelse (App\Models\Sites::all() as $item)
                                            <option value="{{ $item->siteId }}">{{ $item->name }}</option>
                                        @empty
                                            <option value="" disabled>No sites available...</option>
                                        @endforelse
                                    </select>
                                </div>
                                <hr>
                                <h4 class="text-start">Date Range</h4>
                                <div class="col-md-5">
                                    <label for="startDate" class="form-label text-start w-100">Start Date</label>
                                    <input type="date" name="startDate" id="startDate" class="form-control" required>
                                    <div class="mt-2 text-muted text-start" id="readableStartDate"></div>
                                </div>

                                <div class="col-md-5">
                                    <label for="endDate" class="form-label text-start w-100">End Date</label>
                                    <input type="date" name="endDate" id="endDate" class="form-control" required>
                                    <div class="mt-2 text-muted text-start" id="readableEndDate"></div>
                                </div>


                                <hr>
                                <h4 class="text-start">Additional Information</h4>
                                <!-- Additional Inputs -->
                                <div class="col-md-12">
                                    <label for="project" class="form-label text-start w-100">Project</label>
                                    <textarea name="project" class="form-control" rows="3" required>SUPPLY, DELIVERY, INSTALLATION, AND MAINTENANCE OF MANAGED INTERNET SERVICES FOR THE PROJECT WI-FI IN NORTHERN SAMAR (WINS)</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label for="supplier" class="form-label text-start w-100">Supplier</label>
                                    <input type="text" name="supplier" class="form-control" value="LIBRIFY IT SOLUTIONS" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="acceptanceDate" class="form-label text-start w-100">Acceptance Date</label>
                                    <input type="date" name="acceptanceDate" id="acceptanceDate" class="form-control" value="2024-07-23" required>
                                    <!-- Human-readable date display -->
                                    <div class="mt-2 text-muted text-start" id="readableDate"></div>
                                </div>
                                <hr>
                                <h4 class="text-start">Signatories</h4>
                                <div id="peopleContainer" class="row g-3"></div>

                                <button type="button" class="mt-3 btn btn-sm btn-secondary" onclick="addPerson()">+ Add Person</button>
                                @csrf
                                <div class="col-md-2 align-self-end">
                                    <button type="submit" class="btn btn-primary">Export</button>
                                </div>

                                <script>
                                    function updateReadableDate(inputId, outputId) {
                                        const input = document.getElementById(inputId);
                                        const output = document.getElementById(outputId);

                                        function formatAndDisplay(dateStr) {
                                            if (!dateStr) {
                                                output.textContent = '';
                                                return;
                                            }
                                            const date = new Date(dateStr);
                                            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                                            output.textContent = date.toLocaleDateString(undefined, options);
                                        }

                                        // Initial load
                                        formatAndDisplay(input.value);

                                        // On input change
                                        input.addEventListener('input', () => formatAndDisplay(input.value));
                                    }

                                    updateReadableDate('startDate', 'readableStartDate');
                                    updateReadableDate('endDate', 'readableEndDate');
                                    updateReadableDate('acceptanceDate', 'readableDate'); // for the previous one

                                    // people

                                    const defaultPeople = [
                                        {
                                            purpose: 'Prepared By',
                                            name: 'MEL LAURENCE TUBALLAS',
                                            designation: 'INSTALLER / PROJECT FOCAL'
                                        },
                                        {
                                            purpose: 'Verified by',
                                            name: 'ENGR. CARL ANTHONY C. CATUBAO',
                                            designation: 'FWFA Team Lead'
                                        },
                                        {
                                            purpose: 'Approved by',
                                            name: 'ENGR. GUALBERTO R. GUALBERTO, JR.',
                                            designation: 'FWFA Focal'
                                        }
                                    ];

                                    const peopleContainer = document.getElementById('peopleContainer');

                                    let personIndex = 0; // Keep track of the index

                                    function createPersonRow(purpose = '', name = '', designation = '') {
                                        const row = document.createElement('div');
                                        row.className = 'row g-2 align-items-end mb-2';

                                        // Use the current personIndex for all input names
                                        row.innerHTML = `
                                            <div class="col-md-3">
                                                <label class="form-label text-start">Purpose</label>
                                                <input type="text" name="people[${personIndex}][purpose]" class="form-control" value="${purpose}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-start">Name</label>
                                                <input type="text" name="people[${personIndex}][name]" class="form-control" value="${name}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-start">Designation</label>
                                                <input type="text" name="people[${personIndex}][designation]" class="form-control" value="${designation}" required>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">×</button>
                                            </div>
                                        `;

                                        personIndex++; // Increment index for next row
                                        peopleContainer.appendChild(row);
                                    }

                                    // Load defaults
                                    defaultPeople.forEach(p => createPersonRow(p.purpose, p.name, p.designation));

                                    // Expose to global scope so it works with button
                                    window.addPerson = () => createPersonRow();

                                </script>


                            </form>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='mt-2 col-lg-4 col-md-4 col-sm-12'>
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
                <div class='mt-2 col-lg-4 col-md-4 col-sm-12'>
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
                <div class='mt-2 col-lg-4 col-md-4 col-sm-12'>
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
            </div>

            <div class='mt-5 table-responsive'>
                <table class='table table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th scope='col'>
                            <input type='checkbox' name='' id='' class='checkAll'>
                            </th>
                            <th>Name</th>
                            <th>Region</th>
                            <th>Timezone</th>
                            <th>Scenario</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sites as $item)
                            <tr>
                                <th scope='row'>
                                    <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                </th>
                                <td>
                                    <a href="{{ route('sites.show', $item->siteId) }}" class="nav-link text-primary fw-bold">{{ $item->name }}</a></td>
                                    <td>{{ $item->region }}</td>
                                    <td>{{ $item->timezone }}</td>
                                    <td>{{ $item->scenario }}</td>
                                <td>
                                    <a href='{{ route('sites.show', $item->siteId) }}'><i class='fas fa-eye text-success'></i></a>
                                    {{-- <a href='{{ route('sites.edit', $item->id) }}'><i class='fas fa-edit text-info'></i></a>
                                    <a href='{{ route('sites.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a> --}}
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

    {{ $sites->links('pagination::bootstrap-5') }}

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

                $.post('/sites-delete-all-bulk-data', {
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

                $.post('/sites-move-to-trash-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })
        });
    </script>
@endsection
