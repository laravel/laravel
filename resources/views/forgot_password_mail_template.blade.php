<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">

<div style="width: 80%; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 5px;">
    <div style="text-align: center; padding: 10px; background-color: #007bff; color: white; border-radius: 5px 5px 0 0;">
        <h1>Password Reset</h1>
    </div>
    <div style="padding: 20px;">
        <p>Hello,</p>
        <p>We received a request to reset your password. If you did not make this request, please ignore this email. Otherwise, you can reset your password using the link below.</p>
        <p><a href="    {{ url('reset-password/token=' . $token) }}  " style="color: #007bff;font-size: 30px; ">Reset Password</a></p>
        <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
        <p>
           

        
        </p>
        <p>This link will expire in 24 hours for security reasons.</p>
    </div>
    <div style="text-align: center; padding: 10px; background-color: #007bff; color: white; border-radius: 0 0 5px 5px;">
        <p>If you have any questions, please contact our support team.</p>
    </div>
</div>

</body>
</html>

