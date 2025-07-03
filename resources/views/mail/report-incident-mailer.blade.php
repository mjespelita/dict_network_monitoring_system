<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Site Incident Report</title>
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
    <h2>⚠️ Site Incident Report</h2>
    <p>Hello,</p>
    <p>This is to inform you that one of your monitored sites is currently experiencing issues. Please review the details below:</p>

    <div class="details">
      <p><strong>Site Name:</strong> {{ $name }}</p>
      <p><strong>Site ID:</strong> {{ $siteId }}</p>
      <p><strong>Identified Reason:</strong> {{ $reason }}</p>
      <p><strong>Troubleshooting Performed:</strong> {{ $troubleshoot }}</p>
    </div>

    <p>Our team is actively monitoring this incident and will provide updates as necessary.</p>

    <p>Kind regards,<br>
    <strong>DICT Network Monitoring System</strong></p>

    <div class="footer">
      This is an automated message. Please do not reply.
    </div>
  </div>
</body>
</html>
