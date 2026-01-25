<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Escaner Paquetería</title>

    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #000;
            color: #fff;
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        #reader {
            width: 100%;
            max-width: 600px;
            border: none !important;
        }

        /* Personalización de la interfaz de la librería */
        #reader__scan_region {
            background: rgba(0, 0, 0, 0.5);
        }

        #reader__dashboard_section_csr span {
            display: none !important;
        }

        /* Ocultar selector de cámara nativo feo */

        .status-msg {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
            color: #aaa;
        }

        .loading {
            font-size: 18px;
            font-weight: bold;
            color: #3498db;
        }
    </style>
</head>

<body>

    <div id="reader"></div>

    <div id="status" class="status-msg">Iniciando cámara...</div>

    <script>
        const tg = window.Telegram.WebApp;
        tg.ready();
        tg.expand(); // Expande la webapp al 100% de altura disponible

        // Configuración del escáner
        const config = {
            fps: 10, // Frames por segundo
            qrbox: { width: 250, height: 250 }, // Tamaño del cuadro de enfoque
            aspectRatio: 1.0
        };

        // Función que se ejecuta al detectar un código
        function onScanSuccess(decodedText, decodedResult) {
            // 1. Feedback háptico (vibración) para confirmar lectura
            if (tg.HapticFeedback) {
                tg.HapticFeedback.notificationOccurred('success');
            }

            // 2. Mostrar mensaje visual rápido
            document.getElementById('status').innerText = "✅ Leído: " + decodedText;
            document.getElementById('status').style.color = "#2ecc71";

            // 3. ENVIAR DATOS AL BOT Y CERRAR
            // Esto envía el texto 'decodedText' como un mensaje de servicio al bot
            tg.sendData(decodedText);

            // Opcional: Cerrar manualmente si sendData no lo hace inmediato (aunque suele hacerlo)
            tg.close();
        }

        function onScanFailure(error) {
            // No hacer nada para no saturar, el escaneo sigue intentando
        }

        // Iniciar el escáner
        const html5QrcodeScanner = new Html5QrcodeScanner("reader", config, /* verbose= */ false);

        // Renderizar
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        // Actualizar estado visual
        setTimeout(() => {
            document.getElementById('status').innerText = "Apunta al código QR o de Barras";
        }, 1000);

    </script>
</body>

</html>