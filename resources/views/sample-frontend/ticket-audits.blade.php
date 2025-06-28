
{{-- @extends('layouts.main') --}}







@extends('layouts.main')

@section('content')

    <h1>Ticket Logs</h1>

    <div class='card'>
        <div class='card-body'>

            <div class="list-group">
                @forelse ($audits as $audit)
                    <div class="list-group-item mb-2">
                        <p class="mb-1">
                            <strong>{{ $audit->user ? $audit->user->name : 'System' }}</strong>
                            {{ $audit->event }}
                            <strong>{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</strong>
                            on {{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($audit->created_at) }}
                        </p>

                        @php
                            $pairs = explode(',', $audit->tags);
                            $tagsArray = [];
                            foreach ($pairs as $pair) {
                                $parts = explode(':', $pair, 2);
                                if(count($parts) == 2) {
                                    $tagsArray[$parts[0]] = $parts[1];
                                }
                            }
                        @endphp

                        {{-- <p>Sites ID: {{ $tagsArray['sites_id'] ?? 'Not found' }}</p> --}}

                        <pre>{{ $audit->tags }}</pre>

                        <a href="{{ url('/show-sites/'.$tagsArray['sites_id'] ?? 'Not Found') }}">
                            <button class="btn btn-success">View Details</button>
                        </a>

                        @if ($audit->event === 'updated')
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Field</th>
                                            <th>Old Value</th>
                                            <th>New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($audit->new_values as $key => $value)
                                            @php
                                                $old = $audit->old_values[$key] ?? 'N/A';
                                            @endphp
                                            <tr>
                                                <td>{{ ucfirst($key) }}</td>
                                                <td class="text-danger">{{ $old }}</td>
                                                <td class="text-success">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif ($audit->event === 'created')
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Field</th>
                                            <th>New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($audit->new_values as $key => $value)
                                            <tr>
                                                <td>{{ ucfirst($key) }}</td>
                                                <td class="text-success">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif ($audit->event === 'deleted')
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Field</th>
                                            <th>Deleted Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($audit->old_values as $key => $value)
                                            <tr>
                                                <td>{{ ucfirst($key) }}</td>
                                                <td class="text-danger">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif


                        <small class="text-muted">
                            IP: {{ $audit->ip_address }} | URL: {{ $audit->url }} | Agent: {{ Str::limit($audit->user_agent, 40) }}
                        </small>
                    </div>
                @empty
                    <p class="text-center text-muted">No audit logs available.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $audits->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
