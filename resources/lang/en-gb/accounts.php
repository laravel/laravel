<?php

return [
    'register' => [
        'forgot_password' => 'Forgotten your password?',
        'login' => 'Already have an account?',
        'button' => 'Register',
        'labels' => [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Password confirmation',
        ],
        'placeholders' => [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Password confirmation',
        ],
    ],
    'login' => [
        'forgot_password' => 'Forgotten your password?',
        'register' => 'Register for an account',
        'button' => 'Login',
        'labels' => [
            'email' => 'Email',
            'password' => 'Password',
        ],
        'placeholders' => [
            'email' => 'Email',
            'password' => 'Password',
        ],
    ],
    'passwords' => [
        'password' => 'Passwords must be at least 8 characters and match the confirmation.',
        'reset' => 'Your password has been reset!',
        'sent' => 'If your email address exists in our database, you will receive a reset link at your email address in a few minutes.',
        'token' => 'This password reset token is invalid.',
        'user' => 'If your email address exists in our database, you will receive a reset link at your email address in a few minutes.',
        'email' => [
            'subject' => 'Reset Password Notification',
            'title' => 'Password Reset',
            'message' => 'You are receiving this email because we received a password reset request for your account.',
            'button' => 'Reset Password',
            'ignore' => 'If you did not request a password reset, no further action is required.',
        ],
    ],
    'verification' => [
        'sent' => 'Please check your email address for your verification link',
        'resent' => 'If your email address exists in our database, you will receive a verify link at your email address in a few minutes.',
        'resend_label' => 'Resend verify link',
        'email' => [
            'subject' => 'Email verification',
            'title' => 'Thanks for signing up!',
            'message' => 'To get started, click the link below to confirm your account.',
            'button' => 'Verify my account',
        ],
        'confirmation' => 'Your account has now been verified, please login.',
    ],
];
