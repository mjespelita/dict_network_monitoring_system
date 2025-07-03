<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Device Restoration Notice</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e6f4ea;
      color: #2d4739;
      padding: 20px;
      line-height: 1.6;
    }
    .email-container {
      background-color: #ffffff;
      padding: 20px;
      border: 1px solid #d4edda;
      border-radius: 5px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    h2 {
      color: #28a745;
    }
    .details {
      background-color: #f0fdf4;
      padding: 10px 15px;
      border-left: 5px solid #28a745;
      border-radius: 4px;
      margin-top: 10px;
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
    <h2>✅ Device Restored Successfully</h2>
    <p>Hello,</p>
    <p>We’re pleased to notify you that one of your monitored devices has successfully <strong>come back online</strong>.</p>

    <div class="details">
      <p><strong>Site Name:</strong> {{ $name }}</p>
      <p><strong>Device Name:</strong> {{ $deviceName }}</p>
      <p><strong>Device MAC:</strong> {{ $deviceMac }}</p>
      <p><strong>Device Type:</strong> {{ $deviceType }}</p>
      <p><strong>Status:</strong> {{ $status }}</p>
      <p><strong>Site ID:</strong> {{ $siteId }}</p>
      <p><strong>Ticket Number:</strong> {{ $ticket_number }}</p>
      <p><strong>Reason:</strong> {{ $reason }}</p>
      <p><strong>Troubleshoot:</strong> {{ $troubleshoot }}</p>
    </div>

    <p>Our system will continue to monitor the device and notify you of any further changes.</p>

    <p>Warm regards,<br>
    <strong>DICT Network Monitoring System</strong></p>

    <div class="footer">
      This is an automated message. Please do not reply.
    </div>
  </div>
</body>
</html>
