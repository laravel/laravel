<?php

return [
    'reset' => '¡Tu contraseña ha sido restablecida!',
    'sent' => '¡Hemos enviado por correo electrónico el enlace para restablecer tu contraseña!',
    'throttled' => 'Por favor espera antes de intentar de nuevo.',
    'token' => 'Este token de restablecimiento de contraseña es inválido.',
    'user' => "No podemos encontrar un usuario con esa dirección de correo electrónico.",

    // Personaliza el email
    'email' => [
        'subject' => 'Notificación de restablecimiento de contraseña',
        'greeting' => '¡Hola!',
        'line1' => 'Estás recibiendo este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.',
        'action' => 'Restablecer contraseña',
        'line2' => 'Este enlace de restablecimiento de contraseña expirará en :count minutos.',
        'line3' => 'Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna otra acción.',
        'salutation' => 'Saludos, :app_name',
    ],
];