<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Device Offline Report</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fff8e6;
      color: #5a3e00;
      padding: 20px;
      line-height: 1.6;
    }
    .email-container {
      background-color: #ffffff;
      padding: 20px;
      border: 1px solid #ffeeba;
      border-radius: 5px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    h2 {
      color: #d39e00;
    }
    .details {
      background-color: #fff3cd;
      padding: 10px 15px;
      border-left: 5px solid #ffc107;
      border-radius: 4px;
      margin-top: 10px;
    }
    .highlight {
      background-color: #fff8e1;
      padding: 10px;
      border-radius: 4px;
      border: 1px dashed #ffc107;
      margin-top: 15px;
    }
    .footer {
      font-size: 12px;
      color: #6c757d;
      margin-top: 30px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <h2>⚠️ Device Still Offline</h2>
    <p>Hello,</p>
    <p>We’d like to inform you that one of your monitored devices is still currently <strong>offline</strong>. Please find below the latest details and troubleshooting actions taken so far:</p>

    <div class="details">
      <p><strong>Site Name:</strong> {{ $name }}</p>
      <p><strong>Device Name:</strong> {{ $deviceName }}</p>
      <p><strong>Device MAC:</strong> {{ $deviceMac }}</p>
      <p><strong>Device Type:</strong> {{ $deviceType }}</p>
      <p><strong>Status:</strong> {{ $status }}</p>
      <p><strong>Site ID:</strong> {{ $siteId }}</p>
    </div>

    <div class="highlight">
      <p><strong>Identified Reason:</strong> {{ $reason }}</p>
      <p><strong>Troubleshooting Performed:</strong> {{ $troubleshoot }}</p>
    </div>

    <p>We are continuing to monitor the device and will notify you of any updates or resolution progress.</p>

    <p>Best regards,<br>
    <strong>DICT Network Monitoring System</strong></p>

    <div class="footer">
      This is an automated message. Please do not reply.
    </div>
  </div>
</body>
</html>
