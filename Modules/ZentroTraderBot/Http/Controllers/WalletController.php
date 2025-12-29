<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Necesario para rpcCall
use Elliptic\EC;
use kornrunner\Keccak;
use kornrunner\Ethereum\Transaction;
use Modules\TelegramBot\Entities\Actors;
use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;

class WalletController extends Controller
{
    /**
     * GENERAR NUEVA WALLET
     * Se llama cuando el usuario inicia el bot (/start).
     */
    public function generateWallet(int $userId)
    {
        // 1. Buscar el Actor por su ID de Telegram (user_id)
        $actor = Actors::where('user_id', $userId)->first();

        // Si no existe el actor, lo creamos (Opcional, depende de tu flujo de registro)
        if (!$actor) {
            // $actor = Actors::create(['user_id' => $userId, 'data' => []]); 
            return ['status' => 'error', 'message' => 'Usuario no registrado en el sistema.'];
        }

        $currentData = $actor->data ?? [];

        // Validación: Si ya tiene wallet, devolvemos la existente
        if (isset($currentData['wallet']['address'])) {
            return [
                'status' => 'exists',
                'address' => $currentData['wallet']['address']
            ];
        }

        try {
            // 2. Generar Claves
            $ec = new EC('secp256k1');
            $keyPair = $ec->genKeyPair();
            $privateKeyHex = $keyPair->getPrivate('hex');
            $publicKeyHex = $keyPair->getPublic(false, 'hex');

            // Derivar Address
            $pubKeyNoPrefix = substr($publicKeyHex, 2);
            $hash = Keccak::hash(hex2bin($pubKeyNoPrefix), 256);
            $address = '0x' . substr($hash, -40);

            // 3. GUARDADO SEGURO EN JSON
            $walletData = [
                'address' => $address,
                'private_key' => Crypt::encryptString($privateKeyHex), // 🔒 ENCRIPTADO
                'created_at' => now()->toIso8601String()
            ];

            $currentData['wallet'] = $walletData;
            $actor->data = $currentData;
            $actor->save();

            Log::info("✅ Wallet generada en JSON para usuario $userId");

            return ['status' => 'created', 'address' => $address];

        } catch (\Exception $e) {
            Log::error("❌ Error generando wallet: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error interno de criptografía'];
        }
    }

    /**
     * CONSULTAR SALDO REAL (On-Chain)
     * Conecta con la RPC y obtiene saldos nativos y tokens configurados.
     * * @param int $userId ID del usuario de Telegram
     * @param int $chainId ID de la red (137 = Polygon, 56 = BSC)
     */
    public function getBalance(int $userId, string $networkSymbol = 'POL')
    {
        // NORMALIZACIÓN: Forzamos mayúsculas (pol -> POL)
        $networkSymbol = strtoupper($networkSymbol);

        // 1. Obtener Wallet del Usuario
        $actor = Actors::where('user_id', $userId)->first();
        if (!$actor || !isset($actor->data['wallet']['address'])) {
            return ['status' => 'error', 'message' => 'No tienes wallet configurada. Ejecuta /start.'];
        }
        $address = $actor->data['wallet']['address'];

        // 2. DEDUCIR RED A PARTIR DEL SÍMBOLO
        // Buscamos 'POL' o 'BNB' en la lista de tokens para saber su chain_id
        $tokenConfig = config("zentrotraderbot.tokens.$networkSymbol");

        if (!$tokenConfig) {
            return ['status' => 'error', 'message' => "No se reconoce el símbolo de red: $networkSymbol"];
        }

        $chainId = $tokenConfig['chain_id'];

        // Cargamos la info completa de la red usando el ID deducido
        $network = config("zentrotraderbot.networks.$chainId");
        if (!$network) {
            return ['status' => 'error', 'message' => "Configuración de red incompleta para ID $chainId."];
        }

        $rpcUrl = $network['rpc_url'];

        // 3. OBTENER SALDOS (Lógica idéntica a la anterior)

        // A. Saldo Nativo (POL/BNB)
        $nativeHex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$address, 'latest']);
        $nativeBalance = $this->hexToDec($nativeHex, 18);

        $balances = [
            'network' => $network['name'],     // Ej: Polygon
            'base_asset' => $networkSymbol,    // Ej: POL
            'address' => $address,
            'assets' => []
        ];

        // Guardamos nativo
        $balances['assets'][$network['native_symbol']] = $nativeBalance;

        // B. Saldos de Tokens ERC-20 de esa red
        $allTokens = config('zentrotraderbot.tokens');

        foreach ($allTokens as $symbol => $tokenData) {
            // Filtramos: Que sea de ESTA red y que NO sea el nativo repetido
            if ($tokenData['chain_id'] == $chainId && $tokenData['address'] !== '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee') {

                $tokenBalance = $this->getErc20Balance(
                    $rpcUrl,
                    $tokenData['address'],
                    $address,
                    $tokenData['decimals']
                );

                // Opcional: Solo mostrar si > 0
                // if ($tokenBalance !== '0') {
                $balances['assets'][$symbol] = $tokenBalance;
                // }
            }
        }

        return $balances;
    }

    /**
     * OBTENER CLAVE PRIVADA (DESCIFRADA)
     * Uso interno para firmar transacciones.
     */
    public function getDecryptedPrivateKey(int $userId)
    {
        $actor = Actors::where('user_id', $userId)->first();

        if (!$actor || !isset($actor->data['wallet']['private_key'])) {
            throw new \Exception("Usuario $userId no tiene wallet.");
        }

        $encryptedKey = $actor->data['wallet']['private_key'];

        // 🔓 Desencriptamos manualmente
        return Crypt::decryptString($encryptedKey);
    }

    /**
     * RETIRAR FONDOS (Inteligente)
     * - Deduce la red automáticamente.
     * - Si $amount es null, calcula el máximo posible (Sweep).
     * - Maneja Native y ERC20.
     */
    public function withdraw(int $userId, string $toAddress, string $tokenSymbol, ?float $amount = null)
    {
        // NORMALIZACIÓN: Forzamos mayúsculas (pol -> POL)
        $tokenSymbol = strtoupper($tokenSymbol);

        // 1. OBTENER USUARIO Y CREDENCIALES
        try {
            $privateKey = $this->getDecryptedPrivateKey($userId);
            $fromAddress = $this->getAddressFromKey($privateKey); // Necesario para nonce y checks
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        // 2. OBTENER CONFIGURACIÓN DEL TOKEN Y RED
        $tokenConfig = config("zentrotraderbot.tokens.$tokenSymbol");
        if (!$tokenConfig) {
            return ['status' => 'error', 'message' => "Token $tokenSymbol no configurado."];
        }

        $chainId = $tokenConfig['chain_id'];
        $network = config("zentrotraderbot.networks.$chainId");
        $rpcUrl = $network['rpc_url'];
        $isNative = $tokenConfig['address'] === '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee';

        Log::info("💸 Retiro solicitado: Usuario $userId | Token $tokenSymbol | Chain $chainId");

        try {
            // 3. DATOS DE LA RED (Gas Price y Nonce)
            $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$fromAddress, 'pending']);
            $gasPriceHex = $this->rpcCall($rpcUrl, 'eth_gasPrice', []);

            // Convertimos Gas Price a decimal para cálculos (Wei)
            $gasPriceWei = hexdec($gasPriceHex);

            // Definimos Límite de Gas (Estimación segura)
            // Nativo: 21,000 | ERC20: 65,000 (transferencia estándar suele ser ~50k)
            $gasLimitDec = $isNative ? 21000 : 65000;
            $gasLimitHex = $this->decToHex($gasLimitDec);

            // Costo Total del Gas en Wei
            $totalGasCostWei = bcmul((string) $gasPriceWei, (string) $gasLimitDec);

            // 4. PREPARACIÓN DE MONTOS (Lógica de "Retirar Todo")
            $valueToSendHex = '0x0'; // Valor en el campo 'value' de la TX (Solo para nativos)
            $data = ''; // Payload (Solo para ERC20)

            // --- CASO A: TOKEN NATIVO (POL, BNB) ---
            if ($isNative) {
                // Obtenemos saldo real actual en Wei
                $balanceHex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$fromAddress, 'latest']);
                $balanceWei = $this->hexToDecString($balanceHex); // Helper para string exacto

                if ($amount === null) {
                    // MODO SWEEP: Retirar TODO (Saldo - Gas)
                    $amountWei = bcsub($balanceWei, $totalGasCostWei);
                    if (bccomp($amountWei, '0') <= 0) {
                        return ['status' => 'error', 'message' => 'Saldo insuficiente para cubrir el gas.'];
                    }
                } else {
                    // MODO NORMAL: Monto específico
                    $amountWei = bcmul((string) $amount, bcpow('10', '18'));
                    // Validar que (Monto + Gas) <= Balance
                    $totalRequired = bcadd($amountWei, $totalGasCostWei);
                    if (bccomp($totalRequired, $balanceWei) > 0) {
                        return ['status' => 'error', 'message' => 'Fondos insuficientes (Monto + Gas supera saldo).'];
                    }
                }

                $valueToSendHex = $this->decToHex($amountWei);

                // --- CASO B: TOKEN ERC-20 (USDC, USDT, ETC) ---
            } else {
                // 1. Verificar si hay Gas Nativo suficiente (El usuario paga el gas en POL/BNB)
                $nativeBalanceHex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$fromAddress, 'latest']);
                $nativeBalanceWei = $this->hexToDecString($nativeBalanceHex);

                if (bccomp($nativeBalanceWei, $totalGasCostWei) < 0) {
                    return ['status' => 'error', 'message' => "No tienes suficiente {$network['native_symbol']} para pagar el gas de la transacción."];
                }

                // 2. Calcular monto del Token
                if ($amount === null) {
                    // MODO SWEEP: Consultar balance total del token
                    $amountTokenWei = $this->getRawErc20Balance($rpcUrl, $tokenConfig['address'], $fromAddress);
                    if ($amountTokenWei == '0') {
                        return ['status' => 'error', 'message' => 'No tienes saldo de este token para retirar.'];
                    }
                } else {
                    $decimals = $tokenConfig['decimals'];
                    $amountTokenWei = bcmul((string) $amount, bcpow('10', (string) $decimals));
                }

                // 3. Construir Data (transfer function)
                // Method ID: 0xa9059cbb (transfer)
                // Param 1: Address (padding 64 chars)
                // Param 2: Amount (padding 64 chars)
                $methodId = '0xa9059cbb';
                $paddedAddress = str_pad(substr($toAddress, 2), 64, '0', STR_PAD_LEFT);
                $paddedAmount = str_pad($this->decToHexNoPrefix($amountTokenWei), 64, '0', STR_PAD_LEFT);

                $data = $methodId . $paddedAddress . $paddedAmount;

                // En transacciones ERC20, el 'value' (nativo enviado) es 0
                $valueToSendHex = '0x0';

                // El destinatario real de la TX es el CONTRATO, no el usuario final
                // (El usuario final está encoded dentro de $data)
                $targetContractAddress = $tokenConfig['address'];
            }

            // 5. CONSTRUIR Y FIRMAR TRANSACCIÓN
            // Si es ERC20, el 'to' es el contrato. Si es Nativo, el 'to' es el usuario destino.
            $finalTo = $isNative ? $toAddress : $targetContractAddress;

            $tx = new Transaction(
                $nonceHex,
                $this->decToHex($gasPriceWei),
                $gasLimitHex,
                $finalTo,
                $valueToSendHex,
                $data
            );

            $signedTx = $tx->getRaw($privateKey, $chainId);

            // 6. ENVIAR
            $txHash = $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx]);

            // Manejo de errores de RPC
            if (isset($txHash['error'])) {
                throw new \Exception("RPC Error: " . json_encode($txHash['error']));
            }
            // A veces rpcCall devuelve el hash directo o null si falla
            if (!$txHash || strlen($txHash) < 10) {
                throw new \Exception("Error desconocido al enviar TX.");
            }

            return [
                'status' => 'success',
                'tx_hash' => $txHash,
                'explorer' => $network['explorer'] . $txHash,
                'amount_sent' => ($amount === null) ? 'MAX' : $amount,
                'fee_paid_est' => bcdiv($totalGasCostWei, bcpow('10', '18'), 6) . ' ' . $network['native_symbol']
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error Retiro: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // --- Helpers Privados ---

    private function getAddressFromKey($privateKey)
    {
        $ec = new EC('secp256k1');
        $keyPair = $ec->keyFromPrivate($privateKey);
        $pubKey = $keyPair->getPublic(false, 'hex');
        $hash = Keccak::hash(hex2bin(substr($pubKey, 2)), 256);
        return '0x' . substr($hash, -40);
    }

    /**
     * Consulta balance ERC20 ("balanceOf")
     */
    private function getErc20Balance($rpcUrl, $contractAddress, $walletAddress, $decimals)
    {
        $methodId = '0x70a08231'; // Keccak('balanceOf(address)') -> primeros 4 bytes
        // Rellenamos la dirección a 32 bytes (64 caracteres)
        $paddedAddress = str_pad(substr($walletAddress, 2), 64, '0', STR_PAD_LEFT);
        $data = $methodId . $paddedAddress;

        $responseHex = $this->rpcCall($rpcUrl, 'eth_call', [
            ['to' => $contractAddress, 'data' => $data],
            'latest'
        ]);

        if (!$responseHex || $responseHex === '0x')
            return 0;

        return $this->hexToDec($responseHex, $decimals);
    }

    /**
     * Convierte Hexadecimal a Decimal Humano con precisión
     * Maneja números grandes usando BCMath si es posible o float simple.
     */
    private function hexToDec($hex, $decimals)
    {
        if ($hex === '0x0')
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

    private function rpcCall($url, $method, $params)
    {
        $response = Http::post($url, ['jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => 1]);
        return $response->json()['result'];
    }

    private function decToHex($decimal)
    {
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


    private function hexToDecString($hex)
    {
        if ($hex === '0x0')
            return '0';
        return (string) hexdec(str_replace('0x', '', $hex));
    }

    private function getRawErc20Balance($rpcUrl, $contract, $owner)
    {
        $data = '0x70a08231' . str_pad(substr($owner, 2), 64, '0', STR_PAD_LEFT);
        $res = $this->rpcCall($rpcUrl, 'eth_call', [['to' => $contract, 'data' => $data], 'latest']);
        return $this->hexToDecString($res);
    }

    private function decToHexNoPrefix($decimal)
    {
        $hex = '';
        while (bccomp($decimal, '0') > 0) {
            $rem = bcmod($decimal, '16');
            $decimal = bcdiv($decimal, '16', 0);
            $hex = dechex($rem) . $hex;
        }
        return $hex ?: '0';
    }

    /**
     * MIGRACIÓN DE CLAVES (UTILIDAD DE UN SOLO USO)
     * Re-encripta las wallets de la APP_KEY vieja a la actual.
     * * @param string $oldKey La APP_KEY completa del proyecto anterior (ej: "base64:UjH...")
     */
    public function migrateFromOldKey($oldKeyInput)
    {
        // 2. Preparar el Encriptador "Viejo"
        try {
            // Laravel guarda las keys con el prefijo "base64:", hay que decodificarlo
            if (str_starts_with($oldKeyInput, 'base64:')) {
                $keyRaw = base64_decode(substr($oldKeyInput, 7));
            } else {
                $keyRaw = $oldKeyInput; // Por si acaso la pasas sin base64 (raro)
            }

            // Creamos una instancia manual que sabe desencriptar con la llave vieja
            $oldEncrypter = new Encrypter($keyRaw, 'AES-256-CBC');
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'La clave antigua tiene un formato inválido.']);
        }

        // 3. Buscar Actores con Wallet
        // Filtramos (rudimentariamente) los que tienen data
        $actors = Actors::whereNotNull('data')->get();

        $migratedCount = 0;
        $errors = [];

        foreach ($actors as $actor) {
            // Verificamos si tiene wallet configurada en el JSON
            if (!isset($actor->data['wallet']['private_key'])) {
                continue;
            }

            $currentData = $actor->data;
            $encryptedKey = $currentData['wallet']['private_key'];

            try {
                // A. INTENTO DE DESCIFRADO (CON LLAVE VIEJA)
                // El 'false' le dice: "Lo que vas a encontrar es texto plano, no intentes procesarlo como objeto PHP"
                $decryptedPrivateKey = $oldEncrypter->decrypt($encryptedKey, false);

                // B. RE-ENCRIPTADO (CON LLAVE NUEVA - AUTOMÁTICO)
                // Crypt::encryptString usa la APP_KEY actual del .env nuevo
                $newEncryptedKey = Crypt::encryptString($decryptedPrivateKey);

                // C. ACTUALIZAR Y GUARDAR
                $currentData['wallet']['private_key'] = $newEncryptedKey;

                // Opcional: Marcar que fue migrado para no re-procesar
                $currentData['wallet']['migrated_at'] = now()->toIso8601String();

                $actor->data = $currentData;
                $actor->save();

                $migratedCount++;

            } catch (DecryptException $e) {
                // Si falla, puede ser que:
                // 1. Ya estaba migrada (encriptada con la clave nueva)
                // 2. La clave vieja es incorrecta

                // Probamos si ya funciona con la clave actual para confirmar
                try {
                    Crypt::decryptString($encryptedKey);
                    $errors[] = "Actor ID {$actor->id}: Ya estaba migrado (Ignorado).";
                } catch (\Exception $ex) {
                    $errors[] = "Actor ID {$actor->id}: Falló el descifrado. ¿Clave incorrecta?";
                }
            }
        }

        return response()->json([
            'status' => 'finished',
            'migrated' => $migratedCount,
            'details' => $errors
        ]);
    }
}