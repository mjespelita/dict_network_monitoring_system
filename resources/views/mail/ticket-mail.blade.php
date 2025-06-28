<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Support Ticket</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <table align="center" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <tr style="background-color: #004085; color: #ffffff;">
            <td style="padding: 20px; text-align: center;">
                <h2>Support Ticket Created</h2>
                <p style="margin: 0;">Well begun is half done. – Aristotle</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Hello,</p>
                <p>A new support ticket has been reported. Below are the details:</p>

                <table width="100%" cellpadding="8" cellspacing="0" style="font-size: 14px;">
                    <tr>
                        <td><strong>Site ID:</strong></td>
                        <td>{{ $sites_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Site Name:</strong></td>
                        <td>{{ $name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ticket Number:</strong></td>
                        <td>{{ $ticket_number }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date Reported:</strong></td>
                        <td>{{ $date_reported }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ $address }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nearest Landmark:</strong></td>
                        <td>{{ $nearest_landmark }}</td>
                    </tr>
                    <tr>
                        <td><strong>Issue Reported:</strong></td>
                        <td>{{ $issue }}</td>
                    </tr>
                    <tr>
                        <td><strong>Troubleshooting Done:</strong></td>
                        <td>{{ $troubleshooting }}</td>
                    </tr>
                </table>

                <p style="margin-top: 20px;">Please take the necessary action as soon as possible.</p>
                <p>Thank you!</p>
            </td>
        </tr>
        <tr style="background-color: #f1f1f1;">
            <td style="padding: 15px; text-align: center; font-size: 12px; color: #888888;">
                &copy; {{ date('Y') }} Librify IT Solutions. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
