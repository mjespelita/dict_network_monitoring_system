
<!DOCTYPE html>
<html lang='{{ str_replace('_', '-', app()->getLocale()) }}'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name='csrf-token' content='{{ csrf_token() }}'>
        <meta name='author' content='Mark Jason Penote Espelita'>
        <meta name='keywords' content='keyword1, keyword2'>
        <meta name='description' content='Dolorem natus ab illum beatae error voluptatem incidunt quis. Cupiditate ullam doloremque delectus culpa. Autem harum dolorem praesentium dolorum necessitatibus iure quo. Et ea aut voluptatem expedita.'>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href='{{ url('assets/bootstrap/bootstrap.min.css') }}' rel='stylesheet'>
        <!-- FontAwesome for icons -->
        <link href='{{ url('assets/font-awesome/css/all.min.css') }}' rel='stylesheet'>
        <link rel='stylesheet' href='{{ url('assets/custom/style.css') }}'>
        <link rel='icon' href='{{ url('assets/logo.png') }}'>
    </head>
    <body class='font-sans antialiased'>

        <!-- Sidebar for Desktop View -->
        <div class='sidebar' id='mobileSidebar'>
            <div class='logo'>
                <div class="p-3">
                    <img src='{{ url('assets/librify-logo.png') }}' alt=''> <br>
                </div>
                <div class="p-4">
                    <small>Powered by</small>
                    <img src='{{ url('assets/logo.png') }}' alt='' style="width: 60px !important">
                </div>
            </div>
            <a href='{{ url('dashboard') }}' class='{{ request()->is('dashboard', 'admin-dashboard') ? 'active' : '' }}'><i class='fas fa-tachometer-alt'></i> Dashboard</a>
            <a href='{{ url('customers') }}' class='{{ request()->is('customers', 'trash-customers', 'create-customers', 'show-customers/*', 'edit-customers/*', 'delete-customers/*', 'customers-search*') ? 'active' : '' }}'><i class='fas fa-users'></i> Customers</a>
            <a href='{{ url('sites') }}' class='{{ request()->is('sites', 'trash-sites', 'create-sites', 'show-sites/*', 'edit-sites/*', 'delete-sites/*', 'sites-search*') ? 'active' : '' }}'><i class='fas fa-house'></i> Sites</a>
            {{-- <a href='#' class="cloud-based-systems-menu-toggle-button py-3" style="border-top: 1px solid #dcdcdc;border-bottom: 1px solid #dcdcdc;"><i class='fas fa-cloud'></i> Cloud-Based Systems</a>
            <div class="cloud-based-systems-menu-dropdown" style="display: none">
                <a href='{{ url('logs') }}' class='{{ request()->is('logs', 'create-logs', 'show-logs/*', 'edit-logs/*', 'delete-logs/*', 'logs-search*') ? 'active' : '' }}'><i class='fas fa-bars'></i> Coming Soon</a>
            </div>
            <a href='#' class="on-premise-systems-menu-toggle-button py-3" style="border-bottom: 1px solid #dcdcdc;"><i class='fas fa-cloud'></i> On-Premise Systems</a>
            <div class="on-premise-systems-menu-dropdown" style="display: none">
                <a href='{{ url('logs') }}' class='{{ request()->is('logs', 'create-logs', 'show-logs/*', 'edit-logs/*', 'delete-logs/*', 'logs-search*') ? 'active' : '' }}'><i class='fas fa-bars'></i> Coming Soon</a>
            </div> --}}
            <a href='{{ url('uploads') }}' class='{{ request()->is('uploads', 'trash-uploads', 'create-uploads', 'show-uploads/*', 'edit-uploads/*', 'delete-uploads/*', 'uploads-search*') ? 'active' : '' }}'><i class='fas fa-upload'></i> Data Uploads</a>
            <a href='{{ url('auditlogs') }}' class='{{ request()->is('auditlogs', 'create-auditlogs', 'show-auditlogs/*', 'edit-auditlogs/*', 'delete-auditlogs/*', 'auditlogs-search*') ? 'active' : '' }}'><i class="fas fa-clipboard-list"></i> Audit Logs</a>
            <a href='{{ url('logs') }}' class='{{ request()->is('logs', 'create-logs', 'show-logs/*', 'edit-logs/*', 'delete-logs/*', 'logs-search*') ? 'active' : '' }}'><i class="fas fa-bars"></i> System Logs</a>
            <a href='{{ url('user/profile') }}'><i class='fas fa-user'></i> {{ Auth::user()->name }}</a>
        </div>

        <!-- Top Navbar -->
        <nav class='navbar navbar-expand-lg navbar-dark'>
            <div class='container-fluid'>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav'
                    aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation' onclick='toggleSidebar()'>
                    <i class='fas fa-bars'></i>
                </button>
            </div>
        </nav>

        <x-main-notification />

        <div class='content'>
            @yield('content')
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        {{-- apex charts --}}

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- Bootstrap JS and dependencies -->
        <script src='{{ url('assets/bootstrap/bootstrap.bundle.min.js') }}'></script>

        <!-- Custom JavaScript -->
        <script src="{{ url('assets/custom/script.js') }}"></script>
        <script src="{{ url('assets/pollinator/pollinator.min.js') }}"></script>
        <script src="{{ url('assets/polly.js') }}"></script>
        <script>
            function toggleSidebar() {
                document.getElementById('mobileSidebar').classList.toggle('active');
                document.getElementById('sidebar').classList.toggle('active');
            }

            // toggle menu

            $(document).ready(function () {
                // menu toggle

                $('.cloud-based-systems-menu-toggle-button').click(function () {
                    $('.cloud-based-systems-menu-dropdown').slideToggle()
                })

                $('.on-premise-systems-menu-toggle-button').click(function () {
                    $('.on-premise-systems-menu-dropdown').slideToggle()
                })
            })
            
            const polling = new PollingManager({
                url: "https://jsonplaceholder.typicode.com/todos/2", // API to fetch data
                delay: 5000, // Poll every 5 seconds
                failRetryCount: 3, // Retry on failure
                onSuccess: (data) => {
                    console.log("Success! Fetched data:", data);
                    // Your custom success handling logic
                },
                onError: (error) => {
                    console.error("Error fetching data:", error);
                    // Your custom error handling logic
                }
            });

            // Start polling
            polling.start();

        </script>
    </body>
</html>
