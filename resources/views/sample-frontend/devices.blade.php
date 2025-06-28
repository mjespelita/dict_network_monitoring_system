
@extends('layouts.main')

@section('content')
    <h1>Devices - {{ $item->name }}</h1>

    <x-internal-sidebar :item="$item" />

    <div class='card'>
        <div class='card-body'>

            {{-- <div class="row">
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
            </div> --}}

            <div class='table-responsive'>
                <table class='table table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th>Device Name</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Model</th>
                            <th>Version</th>
                            <th>Uptime</th>
                            <th>CPU %</th>
                            <th>Memory %</th>
                            <th>Public IP</th>
                            <th>Link Speed</th>
                            <th>Duplex</th>
                        </tr>
                    </thead>

                    <tbody id="deviceTableBody">
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 50px 0;">
                                <div style="display: inline-block; width: 3rem; height: 3rem; border: 0.4rem solid #ccc; border-top-color: #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <div style="margin-top: 1rem; color: #888;">Loading devices data...</div>
                            </td>

                            <style>
                                @keyframes spin {
                                    to { transform: rotate(360deg); }
                                }
                                </style>
                        </tr>
                        {{--  <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-065 Allen Plaza</td>
                            <td>192.168.0.1</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.0</td>
                            <td>1.2.0</td>
                            <td>3day(s) 21h 12m 46s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-066 Hilltop Mall</td>
                            <td>192.168.0.2</td>
                            <td>
                                <span class="text-danger">Disconnected</span>
                            </td>
                            <td>ER7212PC v2.0</td>
                            <td>1.3.0</td>
                            <td>1day(s) 12h 15m 23s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-067 East River</td>
                            <td>192.168.0.3</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.1</td>
                            <td>1.2.5</td>
                            <td>2day(s) 15h 8m 11s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-068 Greenfield Park</td>
                            <td>192.168.0.4</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.2</td>
                            <td>1.4.0</td>
                            <td>4day(s) 18h 45m 33s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-069 Riverside Tower</td>
                            <td>192.168.0.5</td>
                            <td>
                                <span class="text-danger">Disconnected</span>
                            </td>
                            <td>ER7212PC v2.0</td>
                            <td>1.3.5</td>
                            <td>5day(s) 10h 29m 15s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-070 Central Plaza</td>
                            <td>192.168.0.6</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.3</td>
                            <td>1.2.8</td>
                            <td>6day(s) 4h 59m 30s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-071 Oceanview</td>
                            <td>192.168.0.7</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.1</td>
                            <td>1.2.2</td>
                            <td>7day(s) 13h 17m 28s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-072 Mountainview</td>
                            <td>192.168.0.8</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v2.0</td>
                            <td>1.4.1</td>
                            <td>8day(s) 10h 39m 58s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-073 Greenhill</td>
                            <td>192.168.0.9</td>
                            <td>
                                <span class="text-danger">Disconnected</span>
                            </td>
                            <td>ER7212PC v2.1</td>
                            <td>1.5.0</td>
                            <td>9day(s) 22h 22m 55s</td>
                        </tr>
                        <tr>
                            <th scope='row'>
                                <img src="{{ url('/assets/router.jpg') }}" alt="" width="80px">
                            </th>
                            <td>PICS-P2-R8-074 Coastal Bay</td>
                            <td>192.168.0.10</td>
                            <td>
                                <span class="text-success">Connected</span>
                            </td>
                            <td>ER7212PC v1.0</td>
                            <td>1.1.8</td>
                            <td>10day(s) 3h 41m 10s</td>
                        </tr> --}}
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {

            function loadDeviceData(siteId) {
                $.ajax({
                    url: `/devices-api/${siteId}`,
                    success: function (res) {
                        const container = $('#deviceTableBody');
                        container.empty();

                        console.log(res);

                        if (res.errorCode === 0 && res.result.data.length > 0) {
                            res.result.data.forEach(device => {
                                const status = device.status === 1 ?
                                    `<span class="text-success">Connected</span>` :
                                    `<span class="text-danger">Disconnected</span>`;

                                    const row = `
                                        <tr>
                                            <td>${device.name || '-'}</td>
                                            <td>${device.ip || '-'}</td>
                                            <td>${device.status === 1 ? '<span class="text-success">Connected</span>' : '<span class="text-danger">Disconnected</span>'}</td>
                                            <td>${device.model || '-'}</td>
                                            <td>${device.firmwareVersion || '-'}</td>
                                            <td>${device.uptime || '-'}</td>
                                            <td>${device.cpuUtil != null ? device.cpuUtil + '%' : '-'}</td>
                                            <td>${device.memUtil != null ? device.memUtil + '%' : '-'}</td>
                                            <td>${device.publicIp || '-'}</td>
                                            <td>${device.linkSpeed ? device.linkSpeed + ' Gbps' : '-'}</td>
                                            <td>${device.duplex === 1 ? 'Half' : device.duplex === 2 ? 'Full' : '-'}</td>
                                        </tr>
                                    `;
                                container.append(row);
                            });
                        } else {
                            $.get('/generate-new-api-token', function (res) {
                                window.location.reload();
                            });
                            container.html(`<tr><td colspan="7" class="text-center text-muted">No device data found.</td></tr>`);
                        }
                    },
                    error: function () {
                        $('#deviceTableBody').html(`<tr><td colspan="7" class="text-danger text-center">Failed to load device data.</td></tr>`);
                    }
                });
            }

            $.get('/traffic-api-access-token', function (token) {
                const siteId = window.location.pathname.split('/').filter(Boolean).pop();
                loadDeviceData(siteId); // Load devices on page load
                console.log(siteId)
            }).fail(function () {
                console.error("Failed to fetch access token.");
            });
        })
    </script>
@endsection
