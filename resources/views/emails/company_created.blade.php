<!DOCTYPE html>
<html>
<head>
    <title>Company Created</title>
</head>
<body>
    <h1>Company Created Successfully</h1>
    <p>Dear {{ $company->owner_name }},</p>
    <p>Your company "{{ $company->company_name }}" has been created successfully and is awaiting validation by the administrators.</p>
    <p>Thank you for registering with us.</p>
</body>
</html>