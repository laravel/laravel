<?php

namespace Modules\Web3\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait BlockchainTools
{
    /**
     * LLAMADA RPC UNIFICADA
     * @param bool $throwOnError 
     * - false (Default): Para WalletController (Loguea y retorna null).
     * - true: Para ZeroExController (Lanza Exception para abortar el Swap).
     */
    public function rpcCall($url, $method, $params, $throwOnError = false)
    {
        try {
            $response = Http::post($url, [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => 1
            ]);

            // 1. Error de Conexión HTTP (404, 500, Timeout)
            if ($response->failed()) {
                $msg = "RPC HTTP Error ($method): " . $response->status();

                if ($throwOnError)
                    throw new \Exception($msg);

                Log::error("❌ $msg");
                return null;
            }

            $data = $response->json();

            // 2. Error Lógico de la Blockchain (Saldo insuficiente, nonce bajo, etc)
            if (isset($data['error'])) {
                $errorMsg = json_encode($data['error']);

                if ($throwOnError)
                    throw new \Exception("RPC Error: " . $errorMsg);

                Log::warning("⚠️ RPC Error ($method): " . $errorMsg);
                return null;
            }

            return $data['result'] ?? null;

        } catch (\Exception $e) {
            // Si $throwOnError es true, dejamos que la excepción suba tal cual
            if ($throwOnError)
                throw $e;

            // Si es false, la atrapamos, logueamos y devolvemos null
            Log::error("❌ RPC Connection Fatal: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Convierte Hexadecimal gigante a String Decimal sin Notación Científica.
     * Reemplazo seguro para hexdec() en Blockchain.
     */
    private function hexToDecString($hex)
    {
        // Protección contra NULL
        if (empty($hex) || $hex === '0x0')
            return '0';

        // 1. Limpieza
        $hex = str_replace('0x', '', $hex);
        if ($hex === '')
            return '0';

        // 2. Iteración manual (BigInteger logic)
        // Convertimos carácter por carácter para evitar floats
        $decimal = '0';
        $len = strlen($hex);

        for ($i = 0; $i < $len; $i++) {
            // Multiplicamos el acumulado por 16 (desplazamiento)
            $decimal = bcmul($decimal, '16');

            // Obtenemos el valor del dígito actual (0-15)
            $digit = hexdec($hex[$i]);

            // Sumamos
            $decimal = bcadd($decimal, (string) $digit);
        }

        return $decimal;
    }

    /**
     * Convierte Decimal a Hexadecimal con prefijo 0x (como direcciones Ethereum que usan 0x)
     */
    public function formatDecimalAsHex($decimal)
    {
        if (strpos($decimal, '0x') === 0)
            return $decimal;
        if ($decimal == 0)
            return '0x0';

        $hex = '';
        while (bccomp($decimal, '0') > 0) {
            $rem = bcmod($decimal, '16');
            $decimal = bcdiv($decimal, '16', 0);
            $hex = dechex($rem) . $hex;
        }
        return '0x' . $hex;
    }


    /**
     * Realiza una conversión Hexadecimal a Decimal Humano con precisión. Diseñado específicamente para balances de criptomonedas/tokens.
     * @param string $hex
     * @param int $decimals
     * @return int|string
     */
    private function formatTokenBalance($hex, $decimals)
    {
        // Agregamos la verificación de NULL o vacío aquí
        if (empty($hex) || $hex === '0x0')
            return '0';

        $cleanHex = str_replace('0x', '', $hex);

        // Convertimos a float nativo de PHP
        // Nota: hexdec maneja enteros, si el balance es gigantesco podría perder precisión en 32-bits,
        // pero para visualización en 64-bits es suficiente.
        $val = hexdec($cleanHex);
        $floatVal = $val / pow(10, $decimals);

        return $this->formatHuman($floatVal);
    }


    /**
     * Formatea un float para que sea legible por humanos
     * Evita 1.4E-9 y 10.50000000
     */
    private function formatHuman($number)
    {
        // 1. Si es extremadamente pequeño (polvo inservible), mostramos 0
        // Ajusta este umbral según tu gusto. 0.00001 suele ser buen límite para USDT.
        if ($number < 0.00000001 && $number > 0) {
            return '0'; // O podrías retornar '~0'
        }

        // 2. Forzamos formato decimal estándar (sin E) con hasta 8 decimales
        // number_format devuelve STRING, que es lo que queremos para el JSON
        $string = number_format($number, 8, '.', '');

        // 3. Limpieza estética: quitamos ceros a la derecha
        // Ej: "10.50000000" -> "10.5"
        // Ej: "100.00000000" -> "100."
        $string = rtrim($string, '0');

        // 4. Si quedó un punto al final, lo quitamos
        // Ej: "100." -> "100"
        $string = rtrim($string, '.');

        // Si al quitar todo quedó vacío (caso raro de 0.00000000), devolvemos 0
        return $string === '' ? '0' : $string;
    }

    /**
     * Hexadecimal sin prefijo 0x (Para data de contratos)
     */
    public function decToHexNoPrefix($decimal)
    {
        $hex = '';
        while (bccomp($decimal, '0') > 0) {
            $rem = bcmod($decimal, '16');
            $decimal = bcdiv($decimal, '16', 0);
            $hex = dechex($rem) . $hex;
        }
        return $hex ?: '0';
    }
}