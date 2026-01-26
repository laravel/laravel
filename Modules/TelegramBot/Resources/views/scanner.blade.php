<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Escaner</title>

    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <style>
        :root {
            --tg-theme-bg-color: #ffffff;
            --tg-theme-text-color: #222222;
            --tg-theme-button-color: #3390ec;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--tg-theme-bg-color);
            color: var(--tg-theme-text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            padding: 20px;
        }

        .loader {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border-left-color: var(--tg-theme-button-color);
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        h2 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        p {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        /* Botón de re-intento por si el usuario cierra el popup por error */
        .btn-retry {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: var(--tg-theme-button-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: none;
            /* Se muestra solo si falla o cierran */
        }
    </style>
</head>

<body>

    <div class="container" id="main-content">
        <div class="loader"></div>
        <h2 id="status-title">Iniciando Escáner...</h2>
        <p id="status-desc">Se abrirá la cámara de Telegram para leer el código.</p>
        <button class="btn-retry" id="retry-btn" onclick="openScanner()">Reabrir Cámara</button>
    </div>

    <script>
        const tg = window.Telegram.WebApp;

        // Configurar la WebApp
        tg.ready();

        function openScanner() {
            tg.showScanQrPopup({ text: "Escanee el codigo" }, function (text) {

                // Obtenemos el bot_name de los parámetros de la URL actual
                const urlParams = new URLSearchParams(window.location.search);
                const botName = urlParams.get('bot');

                fetch("{{ route('telegram-scanner-store') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        code: text,
                        bot: botName,
                        data: tg.initData
                    })
                })
                    .then(r => r.json())
                    .then(data => { if (data.success) tg.close(); });

                return true;
            });
        }

    </script>
</body>

</html>