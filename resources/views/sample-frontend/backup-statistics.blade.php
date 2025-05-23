
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
        {{-- <a href='{{ url('insights/'.$item->siteId) }}' class='{{ request()->is('insights/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-chart-line"></i> Insights
        </a> --}}
        <a href='{{ url('logs/'.$item->siteId) }}' class='{{ request()->is('logs/*', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-clipboard-list"></i> Logs
        </a>
        {{-- <a href='{{ url('customers') }}' class='{{ request()->is('customers', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'>
            <i class="fas fa-file-alt"></i> Reports
        </a> --}}
    </div>

    <div class='card'>
        <div class='card-body'>

            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
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
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">

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

            <div class="p-5">
                <h5>Traffic (MBytes)</h5>
                <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                <div id="lineChartStatisticsTraffic"></div>
            </div>

            <div class="p-5">
                <div class="row">
                    <!-- Offline Days Card -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-calendar-x me-2 text-danger"></i>
                                    List of Offline Days
                                </h5>
                                <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                                <ul class="list-offlines mb-0">
                                    <li>Offline days will show here...</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Upload/Download Card -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-arrow-down-up me-2 text-primary"></i>
                                    Total Upload and Download
                                </h5>
                                <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                                <ul class="list-total-sizes mb-0">
                                    {{-- total sizes --}}
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-arrow-down-up me-2 text-primary"></i>
                                    Percentage Availability
                                </h5>
                                <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                                <ul class="list-percentage-availability mb-0">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5">
                {{-- <div id="lineChartStatisticsPackets"></div> --}}
                <div class="row">
                    <div class="col-lg-6 col md-6 col-sm-12">
                        <h5>Top Device CPU Usage</h5>
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <div id="cpuUsageContainer" class="table-responsive">
                            {{-- data here --}}
                        </div>
                    </div>
                    <div class="col-lg-6 col md-6 col-sm-12">
                        <h5>Top Device Memory Usage</h5>
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <div id="memoryUsageContainer" class="table-responsive">
                            <!-- Memory usage table will be injected here -->
                        </div>
                    </div>
                </div>
            </div>

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
                },
                grid: {
                    borderColor: '#ddd',
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: true } }
                }
            };

            const lineChartStatisticsTraffic = new ApexCharts(document.querySelector("#lineChartStatisticsTraffic"), chartOptions);
            lineChartStatisticsTraffic.render();

            function loadTrafficData(token, startUnix, endUnix) {
                const path = window.location.pathname;
                const segments = path.split('/');
                const siteId = segments.filter(segment => segment.length > 0).pop();
                const url = `/traffic-api/${startUnix}/${endUnix}/${siteId}`;

                console.log(startUnix);
                console.log(endUnix);

                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: {
                        "Authorization": `Bearer ${token}`
                    },
                    success: function (res) {
                        if (!res || !res.result || !res.result.switchTrafficActivities) {
                            console.error("Unexpected API structure:", res);

                            $.get('/generate-new-api-token', function (res) {
                                console.log(res)
                                window.location.reload();
                            })

                            // return;
                        }

                        const traffic = res.result.switchTrafficActivities;

                        // No Data Range -----------------------------------------------------------------------------------

                        $('.list-offlines').html("");

                        // Adjust start and end date range for inclusive comparison
                        const adjustedStart = new Date(startUnix * 1000);
                        adjustedStart.setDate(adjustedStart.getDate() + 1); // subtract 1 day

                        console.log(adjustedStart)

                        const adjustedEnd = new Date(endUnix * 1000);
                        adjustedEnd.setDate(adjustedEnd.getDate() - 1); // add 1 day

                        console.log(adjustedEnd)

                        const dataByDate = {};

                        // Step 1: Group traffic by date
                        traffic.forEach(item => {
                            const date = new Date(item.time * 1000);

                            // Filter only within the adjusted date range
                            if (date >= adjustedStart && date <= adjustedEnd) {
                                const dateStr = date.toISOString().split('T')[0]; // YYYY-MM-DD

                                if (!dataByDate[dateStr]) {
                                    dataByDate[dateStr] = [];
                                }

                                dataByDate[dateStr].push(item);
                            }
                        });

                        // Step 2: Identify fully missing data days
                        const missingDates = [];

                        Object.keys(dataByDate).forEach(dateStr => {
                            const items = dataByDate[dateStr];
                            const hasValidData = items.some(item => item.txData != null && item.dxData != null);

                            if (!hasValidData) {
                                missingDates.push(dateStr);
                            }
                        });

                        // Step 3: Sort and group missing dates into ranges
                        missingDates.sort();

                        const offlineRanges = [];
                        let rangeStart = null;
                        let rangeEnd = null;

                        for (let i = 0; i < missingDates.length; i++) {
                            const currentDate = new Date(missingDates[i]);
                            const previousDate = i > 0 ? new Date(missingDates[i - 1]) : null;

                            if (
                                !rangeStart ||
                                (previousDate && (currentDate - previousDate !== 86400000)) // not consecutive
                            ) {
                                if (rangeStart) {
                                    offlineRanges.push([rangeStart, rangeEnd]);
                                }
                                rangeStart = currentDate;
                            }

                            rangeEnd = currentDate;
                        }

                        // Push final range
                        if (rangeStart) {
                            offlineRanges.push([rangeStart, rangeEnd]);
                        }

                        // Step 4: Print ranges
                        if (offlineRanges.length > 0) {
                            const offlineList = document.querySelector('.list-offlines');

                            offlineRanges.forEach(([start, end]) => {
                                const options = { year: 'numeric', month: 'short', day: '2-digit' };
                                const startStr = start.toLocaleDateString('en-US', options);

                                const adjustedEnd = new Date(end);
                                adjustedEnd.setDate(adjustedEnd.getDate() + 1); // Add 1 day
                                const endStr = adjustedEnd.toLocaleDateString('en-US', options);

                                const li = document.createElement('li');
                                li.textContent = startStr === endStr
                                    ? `📅 ${startStr}`
                                    : `📅 ${startStr} - ${endStr}`;
                                offlineList.appendChild(li);
                            });
                        }

                        // End No Data Range -----------------------------------------------------------------------------------

                        // Sum up the download and upload ----------------------------------------------------------------------

                        function formatDataSize(mbValue) {
                            const bytes = mbValue * 1024 * 1024; // Convert MB to Bytes for uniform scaling
                            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
                            let unitIndex = 0;
                            let size = bytes;

                            while (size >= 1024 && unitIndex < units.length - 1) {
                                size /= 1024;
                                unitIndex++;
                            }

                            return `${size.toFixed(2)} ${units[unitIndex]}`;
                        }

                        let totalTx = 0;
                        let totalDx = 0;

                        traffic.forEach(item => {
                            if ('txData' in item && 'dxData' in item) {
                                totalTx += Number(item.txData); // already in MB
                                totalDx += Number(item.dxData);
                            }
                        });

                        const uploadFormatted = formatDataSize(totalTx);
                        const downloadFormatted = formatDataSize(totalDx);

                        const listEl = document.querySelector(".list-total-sizes");
                        listEl.innerHTML = `
                            <li><strong>📤 Upload/Transmitted:</strong> <b class="text-success">${uploadFormatted}</b></li>
                            <li><strong>📥 Download/Received:</strong> <b class="text-success">${downloadFormatted}</b></li>
                        `;

                        // End Sum up the download and upload ----------------------------------------------------------------------

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
                            }
                        });

                        // Load CPU usage table
                        loadCpuUsageData(startUnix, endUnix, siteId);
                        loadMemoryUsageData(startUnix, endUnix, siteId);

                        // Percentage Availability -------------------------------------------------------------------------------

                        const startDate = new Date(startUnix * 1000);
                        const endDate = new Date(endUnix * 1000);

                        // Extract all unique dates with data
                        const dateMap = {}; // { "YYYY-MM-DD": true }

                        traffic.forEach(item => {
                            const date = new Date(item.time * 1000);
                            const dateKey = date.toISOString().split('T')[0];

                            if (item.txData != null || item.dxData != null) {
                                dateMap[dateKey] = true;
                            }
                        });

                        // Generate list of all dates in the range
                        const allDates = [];
                        let current = new Date(startDate);
                        while (current <= endDate) {
                            const dateKey = current.toISOString().split('T')[0];
                            allDates.push(dateKey);
                            current.setDate(current.getDate() + 1);
                        }

                        // Count no-data days
                        const noData = allDates.filter(date => !dateMap[date]).length;

                        // Calculate stats
                        const daysInFirstMonth = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0).getDate();
                        const totalDaysWithData = daysInFirstMonth - noData;
                        const availabilityPercent = Math.round((totalDaysWithData / daysInFirstMonth) * 100);

                        // Debug logs (optional)
                        console.log("🗓️ Days in first month:", daysInFirstMonth);
                        console.log("❌ Offline/No-data days:", noData);
                        console.log("✅ Final percentage (rounded):", availabilityPercent + "%");

                        // Inject into DOM
                        const list = document.querySelector('.list-percentage-availability');
                        list.innerHTML = `
                            <li><strong>📊 Available Days:</strong> ${totalDaysWithData} / ${daysInFirstMonth}</li>
                            <li><strong>❌ Offline Days:</strong> ${noData}</li>
                            <li><strong>✅ Availability:</strong> <span class="text-success fw-bold">${availabilityPercent}%</span></li>
                        `;

                        // End Percentage Availability ---------------------------------------------------------------------------
                    },
                    error: function (xhr) {
                        console.error("Request failed:", xhr.statusText, xhr);
                    }
                });
            }

            function loadCpuUsageData(start, end, siteId) {
                const url = `/top-cpu-usage-api/${start}/${end}/${siteId}`;

                $.get(url, function (data) {
                    const container = $('#cpuUsageContainer');
                    container.empty();

                    if (data.errorCode === 0 && data.result.length > 0) {
                        const table = $('<table class="table table-sm table-striped"></table>');
                        const thead = `
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>MAC</th>
                                    <th>CPU %</th>
                                    <th>Model</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                        `;
                        table.append(thead);

                        const tbody = $('<tbody></tbody>');
                        data.result.forEach(device => {
                            const row = `
                                <tr>
                                    <td>${device.name}</td>
                                    <td>${device.mac}</td>
                                    <td>${device.cpuUtil}%</td>
                                    <td>${device.model}</td>
                                    <td>${device.type}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        table.append(tbody);
                        container.append(table);
                    } else {
                        container.html('<div class="text-muted">No CPU usage data available.</div>');
                    }
                }).fail(function () {
                    $('#cpuUsageContainer').html('<div class="text-danger">Failed to load CPU usage data.</div>');
                });
            }

            function loadMemoryUsageData(start, end, siteId) {
                $.get(`/top-memory-usage-api/${start}/${end}/${siteId}`, function (data) {
                    const container = $('#memoryUsageContainer').empty();
                    if (data?.errorCode === 0 && data.result.length) {
                        const table = `
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr><th>Name</th><th>MAC</th><th>Memory %</th><th>Model</th><th>Type</th></tr>
                                </thead>
                                <tbody>
                                    ${data.result.map(device => `
                                        <tr>
                                            <td>${device.name}</td>
                                            <td>${device.mac}</td>
                                            <td>${device.memUsage}%</td>
                                            <td>${device.model}</td>
                                            <td>${device.type}</td>
                                        </tr>`).join('')}
                                </tbody>
                            </table>`;
                        container.html(table);
                    } else {
                        container.html('<div class="text-muted">No memory usage data available.</div>');
                    }
                }).fail(() => {
                    $('#memoryUsageContainer').html('<div class="text-danger">Failed to load memory usage data.</div>');
                });
            }

            // On page load: fetch access token and load data
            $.get('/traffic-api-access-token', function (token) {
                const now = new Date();
                const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);
                const startUnix = Math.floor(todayStart.getTime() / 1000);
                const endUnix = Math.floor(now.getTime() / 1000);

                loadTrafficData(token, startUnix, endUnix);

                $('#dateRangeForm').on('submit', function (e) {
                    e.preventDefault();

                    let startDate = $('#startDate').val(); // original user-selected start
                    let endDate = $('#endDate').val();     // original user-selected end

                    if (!startDate || !endDate) {
                        alert("Please select both start and end dates.");
                        return;
                    }

                    // Store original dates (unmodified)
                    const originalStart = new Date(startDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });
                    const originalEnd = new Date(endDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit'
                    });

                    // Clone the dates for adjustment
                    let adjustedStart = new Date(startDate);
                    let adjustedEnd = new Date(endDate);

                    // Adjust by -1 day and +1 day
                    adjustedStart.setDate(adjustedStart.getDate() - 1);
                    adjustedEnd.setDate(adjustedEnd.getDate());

                    // Convert adjusted dates to Unix timestamps
                    const start = Math.floor(adjustedStart.getTime() / 1000);
                    const end = Math.floor(adjustedEnd.getTime() / 1000);

                    // You can pass both to your function if needed
                    loadTrafficData(token, start, end, originalStart, originalEnd);

                    $(".date-range").text(`${originalStart} - ${originalEnd}`);
                });

            }).fail(function () {
                console.error("Failed to fetch access token.");
            });
        });
    </script>

@endsection
