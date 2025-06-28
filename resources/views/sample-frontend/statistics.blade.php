
@extends('layouts.main')

@section('content')
    <h1>Performance - {{ $item->name }}</h1>

    <x-internal-sidebar :item="$item" />

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
                <div class="row">

                    <div class="col-lg-4 col md-4 col-sm-12">
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <h5>Percentage Availability</h5>
                        <div id="percentageAvailability">
                            <div style="
                                width: 100%;
                                height: 200px;
                                background-color: #e0e0e0;
                                border-radius: 6px;
                                animation: pulse 1.5s infinite;">
                            </div>

                            <style>
                                @keyframes pulse {
                                    0% { background-color: #e0e0e0; }
                                    50% { background-color: #f0f0f0; }
                                    100% { background-color: #e0e0e0; }
                                }
                            </style>

                        </div>
                    </div>
                    <div class="col-lg-4 col md-4 col-sm-12">
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <div id="uploadAndDownloadAverageSpeedDiv" class="table-responsive">
                            <div style="
                                width: 100%;
                                height: 200px;
                                background-color: #e0e0e0;
                                border-radius: 6px;
                                animation: pulse 1.5s infinite;">
                            </div>

                            <style>
                                @keyframes pulse {
                                    0% { background-color: #e0e0e0; }
                                    50% { background-color: #f0f0f0; }
                                    100% { background-color: #e0e0e0; }
                                }
                            </style>

                        </div>
                    </div>
                    <div class="col-lg-4 col md-4 col-sm-12">
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <div id="uploadDownloadTotalDiv" class="table-responsive">
                            <div style="
                                width: 100%;
                                height: 200px;
                                background-color: #e0e0e0;
                                border-radius: 6px;
                                animation: pulse 1.5s infinite;">
                            </div>

                            <style>
                                @keyframes pulse {
                                    0% { background-color: #e0e0e0; }
                                    50% { background-color: #f0f0f0; }
                                    100% { background-color: #e0e0e0; }
                                }
                            </style>

                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <h5>Traffic (MBytes)</h5>
                <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                <div id="lineChartStatisticsTraffic"></div>
            </div>

            <div class="p-5">
                {{-- <div id="lineChartStatisticsPackets"></div> --}}
                <div class="row">
                    <div class="col-lg-6 col md-6 col-sm-12">
                        <h5>Top Device CPU Usage</h5>
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <div id="cpuUsageContainer" class="table-responsive">
                            <div style="
                                width: 100%;
                                height: 200px;
                                background-color: #e0e0e0;
                                border-radius: 6px;
                                animation: pulse 1.5s infinite;">
                            </div>

                            <style>
                                @keyframes pulse {
                                    0% { background-color: #e0e0e0; }
                                    50% { background-color: #f0f0f0; }
                                    100% { background-color: #e0e0e0; }
                                }
                            </style>

                        </div>
                    </div>
                    <div class="col-lg-6 col md-6 col-sm-12">
                        <h5>Top Device Memory Usage</h5>
                        <h6 class="fw-bold">Date Range: <span class="date-range">Today</span></h6>
                        <div id="memoryUsageContainer" class="table-responsive">
                            <div style="
                                width: 100%;
                                height: 200px;
                                background-color: #e0e0e0;
                                border-radius: 6px;
                                animation: pulse 1.5s infinite;">
                            </div>

                            <style>
                                @keyframes pulse {
                                    0% { background-color: #e0e0e0; }
                                    50% { background-color: #f0f0f0; }
                                    100% { background-color: #e0e0e0; }
                                }
                            </style>

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

    {{-- pollinator --}}

    <script src="{{ url('assets/pollinator/pollinator.min.js') }}"></script>

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

                        // other apis

                        // bandwidth speed

                        getBandwidthUsageApi(startUnix, endUnix, siteId);
                        getUploadDownloadTotal(startUnix, endUnix, siteId);
                        getPercentageAvailability(startUnix, endUnix, siteId);
                    },
                    error: function (xhr) {
                        console.error("Request failed:", xhr.statusText, xhr);
                    }
                });
            }

            function getBandwidthUsageApi(start, end, siteId) {
                const url = `/get-bandwidth-usage-api/${start}/${end}/${siteId}`;
                $.get(url, function (res) {

                    $('#uploadAndDownloadAverageSpeedDiv').html(`
                        <div class="card bg-primary mb-3">
                            <div class="card-header">
                                <i class="fas fa-tachometer-alt"></i> Average Bandwidth Speed
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <i class="fas fa-upload"></i> <strong>Upload:</strong> ${res.uploadAvgSpeed}
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-download"></i> <strong>Download:</strong> ${res.downloadAvgSpeed}
                                </p>
                            </div>
                        </div>
                    `);

                }).fail(err => console.log(err));
            }

            function getUploadDownloadTotal(start, end, siteId) {
                const url = `/get-total-upload-download-api/${start}/${end}/${siteId}`;
                $.get(url, function (res) {

                    $('#uploadDownloadTotalDiv').html(`
                        <div class="card bg-primary mb-3">
                            <div class="card-header">
                                <i class="fas fa-tachometer-alt"></i> Total Download And Upload
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <i class="fas fa-upload"></i> <strong>Upload:</strong> ${res.uploadFormatted}
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-download"></i> <strong>Download:</strong> ${res.downloadFormatted}
                                </p>
                            </div>
                        </div>
                    `);

                }).fail(err => console.log(err));
            }

            function getPercentageAvailability(start, end, siteId) {
                const url = `/get-percentage-availability-api/${start}/${end}/${siteId}`;
                $.get(url, function (res) {
                    let color = 'gray';

                    if (res >= 95) {
                        color = 'green'; // High availability
                    } else if (res >= 75) {
                        color = 'orange'; // Medium availability
                    } else {
                        color = 'red'; // Low availability
                    }

                    $('#percentageAvailability').html(`
                        <h1 class="fw-bold" style="font-size: 100px; color: ${color};">${res}%</h1>
                    `);
                }).fail(err => console.log(err));
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

            // const API_ACCESS_TOKEN = new PollingManager({
            //     url: `/traffic-api-access-token`, // API to fetch data
            //     delay: 5000, // Poll every 5 seconds
            //     failRetryCount: 3, // Retry on failure
            //     onSuccess: (token) => {

            //     },
            //     onError: (error) => {
            //         console.error("Error fetching data:", error);
            //         // Your custom error handling logic
            //     }
            // });

            // // Start polling
            // API_ACCESS_TOKEN.start();

            $.get('/traffic-api-access-token', function (token) {
                const now = new Date();
                const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);
                const startUnix = Math.floor(todayStart.getTime() / 1000);
                const endUnix = Math.floor(now.getTime() / 1000);

                loadTrafficData(token, startUnix, endUnix);

                $('#dateRangeForm').on('submit', function (e) {
                    e.preventDefault();

                    let startDate = $('#startDate').val(); // original user-selected start (YYYY-MM-DD)
                    let endDate = $('#endDate').val();     // original user-selected end

                    if (!startDate || !endDate) {
                        alert("Please select both start and end dates.");
                        return;
                    }

                    // Create Date objects from user input
                    let start = new Date(startDate);
                    let end = new Date(endDate);

                    // Set start to 00:00:00.000 (start of the day)
                    start.setHours(0, 0, 0, 0);

                    // Set end to 23:59:59.999 (end of the day)
                    end.setHours(23, 59, 59, 999);

                    // Convert to Unix timestamps (seconds)
                    const startTimestamp = Math.floor(start.getTime() / 1000).toString();
                    const endTimestamp = Math.floor(end.getTime() / 1000).toString();

                    // Format original dates for display (unmodified)
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

                    // Call your function with updated timestamps
                    loadTrafficData(token, startTimestamp, endTimestamp, originalStart, originalEnd);

                    $('.date-range').html(`
                        <b>${originalStart} - ${originalEnd}</b>
                    `);

                    console.log('token: ' + token);
                    console.log('startTimestamp: ' + startTimestamp);
                    console.log('endTimestamp: ' + endTimestamp);
                    console.log('originalStart: ' + originalStart);
                    console.log('originalEnd: ' + originalEnd);
                    console.log('humanReadableStart: ' + new Date(startTimestamp * 1000).toUTCString());
                    console.log('humanReadableEnd: ' + new Date(endTimestamp * 1000).toUTCString());

                });
            }).fail(function () {
                console.error("Failed to fetch access token.");
            });
        });
    </script>

@endsection
