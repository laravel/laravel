<?php

return [
    "mainmenu" => [
        "salutation" => "Bienvenido al :bot_name",
        "referral" => "Enlace de referido",
        "question" => "¿En qué le puedo ayudar hoy?",
    ],
    "adminmenu" => [
        "header" => "Menú de administrador",
        "warning" => "Aquí encontrará herramientas útiles para la gestión integral del bot",
    ],
    "role" => [
        "admin" => "Admin",
    ],
    "options" => [
        "config" => "Configuración",
        "help" => "Ayuda",
        "yes" => "Sí",
        "no" => "No",
        "cancel" => "Cancelar",
        "delete" => "Eliminar",
        "sendannouncement" => "Anuncio",
        "viewusers" => "Usuarios suscritos",
        "backtomainmenu" => "Volver al menú principal",
    ],
    "prompts" => [
        "whatsnext" => "¿Qué desea hacer ahora?",
        "chooseoneoption" => "Escoja una de las siguientes opciones",
        "areyousure" => [
            "header" => "Solicitud de confirmación",
            "warning" => "CUIDADO: Esta acción no se puede revertir",
            "text" => "¿Está seguro que desea continuar?",
        ],
        "notimplemented" => [
            "header" => "Función no implementada",
            "warning" => "Esta función aun no está lista. Estamos trabajando en ella para sacarla en los próximos días.",
        ],
        "userwithnorole" => [
            "header" => "Nuevo usuario suscrito al bot",
            "warning" => "Invitado por",
        ],
        "usernamerequired" => [
            "line1" => "Para usar este bot, por favor configura un nombre de usuario (@usuario) en tu cuenta de Telegram",
            "line2" => "¿Cómo configurarlo?",
            "line3" => "Ve a Configuración (o Ajustes)",
            "line4" => "Selecciona tu perfil y busca la opción Nombre de usuario",
            "line5" => "Elige un nombre único que comience con @",
            "line6" => "Una vez que hayas configurado tu nombre de usuario, haz clic en el siguiente botón",
            "done" => "Listo, ¡ya lo he hecho!",
        ],
    ],
    "errors" => [
        "header" => "Error",
        "unrecognizedcommand" => [
            "text" => "No se que responderle a “:text”",
            "hint" => "Ud puede interactuar con este bot usando /menu o chequee /ayuda para temas de ayuda",
        ],
    ]
];