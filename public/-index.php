<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>We'll Be Back Soon</title>
  <style>
    body {
      background-color: #f2f2f2;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      color: #333;
      text-align: center;
    }
    .container {
      max-width: 500px;
      padding: 40px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h1 {
      font-size: 2rem;
      margin-bottom: 20px;
      color: #17468F;
    }
    p {
      font-size: 1rem;
      line-height: 1.6;
    }
    .footer {
      margin-top: 30px;
      font-size: 0.9rem;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="img">
        <img src="./assets/dict-logo.png" alt="" width="50%">
        <img src="./assets/maintenance.gif" alt="" width="60%">
    </div>
    <h1>Maintenance in Progress</h1>
    <p>Our developer is currently performing system maintenance to implement important updates. We'll be back online shortly—thank you for your patience!</p>
    <div class="footer">
        &copy; <span id="year"></span> DICT Network Monitoring System. All rights reserved.
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
  </div>
</body>
</html>
