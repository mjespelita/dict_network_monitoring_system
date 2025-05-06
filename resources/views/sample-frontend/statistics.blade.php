
@extends('layouts.main')

@section('content')
    <h1>Performance - {{ $item->name }}</h1>

    <!-- Sidebar for Desktop View -->
    <div class='sidebar' id='mobileSidebar'>
        <div class='logo'>
            <div class="p-3">
                <img src='{{ url('assets/librify-logo.png') }}' alt=''> <br>
            </div>
            <div class="p-3">
                <small>Powered by</small>
                <img src='{{ url('assets/logo.png') }}' alt='' style="width: 60px !important">
            </div>
        </div>

        <div class="p-2">
            <button type='button' class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style="width: 100%;">
                {{ $item->name }}
            </button>
            <div class='dropdown-menu' style="width: 95%; border: 1px solid #212529">
                @forelse (App\Models\Sites::all() as $site)
                    <a class='dropdown-item bulk-move-to-trash' href='{{ url('/show-sites/'.$site->siteId) }}'><i class='fa fa-eye'></i> {{ $site->name }}</a>
                @empty
                    <p>No Sites</p>
                @endforelse
            </div>
        </div>

        <a href='{{ url('sites') }}'><i class='fas fa-arrow-left'></i> Back</a>
        <div class="p-2">
            <b class="text-secondary">Monitoring</b>
        </div>
        <a href='{{ url('show-sites/'.$item->siteId) }}' class='{{ request()->is('show-sites/*') ? 'active' : '' }}'><i class='fas fa-tachometer-alt'></i> Dashboard</a>
        <a href='{{ url('statistics/'.$item->siteId) }}' class='{{ request()->is('statistics/*', 'trash-statistics', 'create-statistics', 'show-statistics/*', 'edit-statistics/*', 'delete-statistics/*', 'statistics-search*') ? 'active' : '' }}'>
            <i class="fas fa-line-chart"></i> Statistics
        </a>
        <a href='{{ url('devices/'.$item->siteId) }}' class='{{ request()->is('devices/*', 'trash-devices', 'create-devices', 'show-devices/*', 'edit-devices/*', 'delete-devices/*', 'devices-search*') ? 'active' : '' }}'>
            <i class="fas fa-desktop"></i> Devices
        </a>
        <a href='{{ url('clients/'.$item->siteId) }}' class='{{ request()->is('clients/*', 'trash-clients', 'create-clients', 'show-clients/*', 'edit-clients/*', 'delete-clients/*', 'clients-search*') ? 'active' : '' }}'>
            <i class="fas fa-users"></i> Clients
        </a>
        <a href='{{ url('insights/'.$item->siteId) }}' class='{{ request()->is('insights/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-chart-line"></i> Insights
        </a>
        <a href='{{ url('logs/'.$item->siteId) }}' class='{{ request()->is('logs/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-clipboard-list"></i> Logs
        </a>
        <a href='{{ url('customers') }}' class='{{ request()->is('customers', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </div>

    <div class='card'>
        <div class='card-body'>

            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>SFP WAN/LAN1</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>SFP WAN/LAN2</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>WAN3</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>WAN/LAN4</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN5</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN6</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN7</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN8</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN9</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN10</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN11</button>
                    <button type='button' style="font-size: 12px" class='p-1 btn btn-outline-secondary'>LAN12</button>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <form action='{{ url('/customers-filter') }}' method='get'>
                        <div class='input-group'>
                            <input type='date' class='form-control' id='from' name='from' required> 
                            <b class='pt-2'>- to -</b>
                            <input type='date' class='form-control' id='to' name='to' required>
                            <div class='input-group-append'>
                                <button type='submit' class='btn btn-primary form-control'><i class='fas fa-filter'></i> Filter</button>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
            </div>

            {{-- <div class="p-5">
                <h5>User Counts</h5>
                <div id="lineChartStatisticsUserCounts"></div>
            </div>

            <div class="p-5">
                <h5>Usage (%)</h5>
                <div id="lineChartStatisticsUsage"></div>
            </div> --}}

            <form id="dateRangeForm" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" id="startDate" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" id="endDate" class="form-control" required>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Go</button>
                </div>
            </form>

            <div class="p-5">
                <h5>Traffic (MBytes)</h5>
                <h6>Date Range: <span class="date-range">Today</span></h6>
                <div id="lineChartStatisticsTraffic"></div>
            </div>

            {{-- <div class="p-5">
                <h5>Packets</h5>
                <div id="lineChartStatisticsPackets"></div>
            </div> --}}

        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    {{-- apex charts --}}

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $(document).ready(function () {
            const chartOptions = {
            chart: {
                type: 'line',
                height: 300,
                background: '#ffffff',
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 3,
                    opacity: 0.1
                }
            },
            stroke: { curve: 'smooth' },
            colors: ["#FE9730", "#01B375"],
            series: [
                { name: 'Received', data: [] },
                { name: 'Transmitted', data: [] }
            ],
            xaxis: {
                categories: [],
                tickAmount: 10, // Show at most 10 evenly spaced labels
                labels: {
                    rotate: 0,
                    style: { fontSize: '11px' }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy hh:mm a'
                }
            },
            grid: {
                borderColor: '#ddd',
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: true } }
            }
        };
        
        const lineChartStatisticsTraffic = new ApexCharts(document.querySelector("#lineChartStatisticsTraffic"), chartOptions);
        lineChartStatisticsTraffic.render();

        // Load and update traffic chart data
        function loadTrafficData(token, startUnix, endUnix) {

            const path = window.location.pathname;
    
            // Split the path by '/' to get the segments
            const segments = path.split('/');
            
            // The ID is the last segment (excluding any empty segment that may exist at the end of the URL)
            const siteId = segments.filter(segment => segment.length > 0).pop();

            const url = `/traffic-api/${startUnix}/${endUnix}/${siteId}`;

            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    "Authorization": `Bearer ${token}`
                },
                success: function (res) {
                    if (!res || !res.result || !res.result.switchTrafficActivities) {
                        console.error("Unexpected API structure:", res);
                        return;
                    }

                    const traffic = res.result.switchTrafficActivities;
                    const aggregated = {};

                    // Aggregate traffic by hour
                    traffic.forEach(item => {
                        const roundedTime = Math.floor(item.time / 3600) * 3600;
                        if (!aggregated[roundedTime]) {
                            aggregated[roundedTime] = { tx: 0, dx: 0 };
                        }
                        aggregated[roundedTime].tx += item.txData || 0;
                        aggregated[roundedTime].dx += item.dxData || 0;
                    });

                    const sortedTimes = Object.keys(aggregated).sort((a, b) => a - b);
                    const inbound = [];
                    const outbound = [];
                    const timestamps = [];

                    let lastDateStr = '';

                    sortedTimes.forEach(time => {
                        const d = new Date(time * 1000);
                        const dateStr = d.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit'
                        });
                        const timeStr = d.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });

                        const label = (dateStr === lastDateStr) ? timeStr : `${dateStr} ${timeStr}`;
                        lastDateStr = dateStr;

                        timestamps.push(label);
                        inbound.push(Number(aggregated[time].tx.toFixed(2)));
                        outbound.push(Number(aggregated[time].dx.toFixed(2)));
                    });

                    lineChartStatisticsTraffic.updateOptions({
                        series: [
                            { name: 'Received', data: inbound },
                            { name: 'Transmitted', data: outbound }
                        ],
                        xaxis: {
                            categories: timestamps,
                            tickAmount: 10,
                            labels: {
                                rotate: 0,
                                style: { fontSize: '11px' }
                            }
                        },
                        tooltip: {
                            x: {
                                format: 'dd MMM yyyy hh:mm a'
                            }
                        }
                    });

                    // Format the date range and display it
                    const startDate = new Date(startUnix * 1000);
                    const endDate = new Date(endUnix * 1000);
                    const formattedStartDate = startDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                    const formattedEndDate = endDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });

                    // If the start and end dates are the same, display "Today - (date today)"
                    if (formattedStartDate === formattedEndDate) {
                        $(".date-range").text(`Today - ${formattedEndDate}`);
                    } else {
                        $(".date-range").text(`${formattedStartDate} - ${formattedEndDate}`);
                    }
                },
                error: function (xhr) {
                    console.error("Request failed:", xhr.statusText, xhr);
                }
            });
        }

        // On load: Fetch token and load today's data
        $.get('/traffic-api-access-token', function (token) {
            const now = new Date();
            const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);
            const startUnix = Math.floor(todayStart.getTime() / 1000);
            const endUnix = Math.floor(now.getTime() / 1000);
            loadTrafficData(token, startUnix, endUnix);

            // Bind form submit for custom date range
            $('#dateRangeForm').on('submit', function (e) {
                e.preventDefault();

                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                if (!startDate || !endDate) {
                    alert("Please select both start and end dates.");
                    return;
                }

                const start = Math.floor(new Date(startDate).getTime() / 1000);
                const end = Math.floor(new Date(endDate).getTime() / 1000);
                loadTrafficData(token, start, end);
            });
        }).fail(function () {
            console.error("Failed to fetch access token.");
        });
        })
    </script>
@endsection
