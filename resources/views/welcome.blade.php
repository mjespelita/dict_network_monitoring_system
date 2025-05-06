<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #2B2B2B; /* Text color */
            background-color: #f4f4f4;
        }

        /* Main brand color */
        .main-bg {
            background-color: #0A5A5A;
            color: white;
        }

        /* Button color */
        .btn-custom {
            background-color: #00A870;
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #008f5a;
        }

        /* Hero section */
        .hero {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0A5A5A;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }

        /* Footer */
        .footer {
            background-color: #0A5A5A;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .footer a {
            color: #00A870;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div>
            <h1>Super Fast Internet, Anytime, Anywhere</h1>
            <p>Experience high-speed, reliable internet with our top-tier services. Sign up today and enjoy seamless connectivity!</p>
            <a href="/login" class="btn btn-custom btn-lg">Login</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container py-5">
        <div class="row">
            <div class="col-md-4 text-center">
                <h3>Fast Speeds</h3>
                <p>Enjoy the fastest internet speeds with no buffering or lag.</p>
            </div>
            <div class="col-md-4 text-center">
                <h3>Reliable Service</h3>
                <p>We offer consistent connectivity for all your devices.</p>
            </div>
            <div class="col-md-4 text-center">
                <h3>24/7 Support</h3>
                <p>Our dedicated support team is here to help anytime, day or night.</p>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <p>&copy; 2025 Internet Service Provider. All rights reserved.</p>
        <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
