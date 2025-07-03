<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Device Disconnected Alert</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      color: #333;
      padding: 20px;
      line-height: 1.6;
    }
    .email-container {
      background-color: #fff;
      padding: 20px;
      border: 1px solid #eee;
      border-radius: 5px;
      max-width: 600px;
      margin: auto;
    }
    h2 {
      color: #d9534f;
    }
    .details {
      background-color: #f2f2f2;
      padding: 10px;
      border-radius: 4px;
      margin-top: 10px;
    }
    .footer {
      font-size: 12px;
      color: #999;
      margin-top: 30px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <h2>🚨 Device Disconnected Alert</h2>
    <p>Hello,</p>
    <p>The monitoring system has detected that a device is currently <strong>disconnected</strong> from the network.</p>

    <div class="details">
      <p><strong>Site Name:</strong> {{ $name }}</p>
      <p><strong>Site ID:</strong> {{ $siteId }}</p>
      <p><strong>Device Name:</strong> {{ $device_name }}</p>
      <p><strong>Device MAC:</strong> {{ $device_mac }}</p>
      <p><strong>Device Type:</strong> {{ $device_type }}</p>
      <p><strong>Status:</strong> {{ $status }}</p>
    </div>

    <p>Please investigate the issue as soon as possible to restore connectivity.</p>

    <p>Regards,<br>
    <strong>DICT Network Monitoring System</strong></p>

    <div class="footer">
      This is an automated message. Please do not reply.
    </div>
  </div>
</body>
</html>
