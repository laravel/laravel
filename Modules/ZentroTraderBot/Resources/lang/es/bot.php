<?php

return [

    "mainmenu" => [
        "description" => "Esta es tu wallet personal en este bot",
    ],
    "actionmenu" => [
        "header" => "Menú de acciones",
        "line1" => "Usando el “Botón de Acción”, puedes cambiar entre 2 niveles",
        "line2" => "Notificaciones: Cuando aparece una señal, el bot solo notifica a los usuarios",
        "line3" => "Ejecutar órdenes: El bot notifica a los usuarios de la comunidad y ejecuta las órdenes correspondientes",
        "line4" => "En este momento la opción “:bot_name” está seleccionada",
    ],
    "subscribtionmenu" => [
        "header" => "Menú de suscripción",
        "line1" => "Aquí puede ajustar sus preferencias",
        "line2" => "Usando el botón “Nivel”, puedes cambiar entre 3 niveles",
        "line3" => "solo recibirás señales de la comunidad",
        "line4" => "solo recibirás tus alertas personales",
        "line5" => "recibirás tanto alertas de la comunidad como las personales",
        "line6" => "Eres un suscriptor de nivel :level",
        "therefore" => "por lo tanto, puedes usar el botón “URL del Cliente” para obtener tu enlace de alertas de TradingView",
    ],
    "options" => [
        "subscribtion" => "Suscripción",
        "subscribtionlevel" => ":icon Nivel :char",
        "clienturl" => "URL de cliente",
        "backtosuscribemenu" => "Volver al menú suscripciones",
        "actionmenu" => "Nivel de acción",
        "actionlevel1" => "Notificaciones",
        "actionlevel2" => "Ejecutar ordenes",
    ],
    "prompts" => [
        "clienturl" => [
            "header" => "Su URL de cliente es la siguiente",
            "warning" => "Esta es la dirección que debe usar en TradingView para notificar al bot que desea trabajar con una alerta personalizada",
            "text" => "¿Está seguro que desea continuar?",
        ],
        "txsuccess" => "TX Exitosa",
        "txfail" => "TX Fallida",
    ],
];