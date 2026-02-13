<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>COE Received</title>
</head>
<body>
    <h2>COE Received</h2>

    <p>Hello {{ $application->student->name }},</p>

    <p>Your Certificate of Eligibility (COE) has been received for your application.</p>

    <p><strong>Application Details:</strong></p>
    <ul>
        <li><strong>Application ID:</strong> {{ $application->application_id }}</li>
        <li><strong>University:</strong> {{ $application->university->name }}</li>
        <li><strong>COE status:</strong> {{ ucfirst(str_replace('_', ' ', $application->coe_status ?? 'received')) }}</li>
        @if($application->coe_received_date)
        <li><strong>COE received date:</strong> {{ \Carbon\Carbon::parse($application->coe_received_date)->format('M d, Y') }}</li>
        @endif
    </ul>

    <p>You can log in to your account to view more details about your application.</p>

    <p>Best regards,<br>
    {{ config('app.name') }} Team</p>
</body>
</html>
