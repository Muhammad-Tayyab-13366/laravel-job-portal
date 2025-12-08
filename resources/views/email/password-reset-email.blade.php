<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foget Password Email</title>
</head>
<body>
    <h1>Hello {{ $mailData['user']->name }}</h1>
    <p>You have requested to reset your password.</p>
    <p>Please click the link below to reset your password:</p>
    <p><a href="{{ route('account.reset-password', $mailData['token']) }}">Reset Password</a></p>    
    <p>Thank you!</p>
    

</body>
</html>