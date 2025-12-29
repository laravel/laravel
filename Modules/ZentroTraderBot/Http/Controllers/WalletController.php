<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Necesario para type hinting
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Necesario para rpcCall
use Elliptic\EC;
use kornrunner\Keccak;
use Modules\TelegramBot\Entities\Actors;
use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;

// 👇 IMPORTANTE: Importamos las dos clases manualmente para compatibilidad con v0.9.0
use kornrunner\Ethereum\Transaction;
use kornrunner\Ethereum\EIP1559Transaction;

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

        // Si no existe el actor, retornamos error (o lo creamos según tu lógica)
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
     * CONSULTAR SALDO (ESTANDARIZADO)
     * Siempre devuelve una estructura 'portfolio' consistente.
     */
    public function getBalance(int $userId, ?string $networkSymbol = null)
    {
        // 1. Obtener Wallet
        $actor = Actors::where('user_id', $userId)->first();
        if (!$actor || !isset($actor->data['wallet']['address'])) {
            return ['status' => 'error', 'message' => 'No tienes wallet configurada.'];
        }
        $address = $actor->data['wallet']['address'];

        $portfolio = [];
        $mode = 'global';

        // --- CASO A: GLOBAL (Todas las redes) ---
        if ($networkSymbol === null) {
            $networks = config('zentrotraderbot.networks');

            foreach ($networks as $chainId => $networkConfig) {
                // Usamos el nombre de la red como clave (Polygon, BSC, etc.)
                $portfolio[$networkConfig['name']] = $this->getBalancesByChainId($address, $chainId, $networkConfig);
            }

            // --- CASO B: ESPECÍFICO (Una sola red) ---
        } else {
            $mode = 'specific';
            $networkSymbol = strtoupper($networkSymbol);

            $tokenConfig = config("zentrotraderbot.tokens.$networkSymbol");
            if (!$tokenConfig)
                return ['status' => 'error', 'message' => "Red desconocida: $networkSymbol"];

            $chainId = $tokenConfig['chain_id'];
            $networkConfig = config("zentrotraderbot.networks.$chainId");

            if (!$networkConfig)
                return ['status' => 'error', 'message' => "Configuración incompleta ID $chainId."];

            // Agregamos SOLO esta red al portafolio
            $portfolio[$networkConfig['name']] = $this->getBalancesByChainId($address, $chainId, $networkConfig);
        }

        // RETORNO UNIFICADO
        return [
            'status' => 'success',
            'mode' => $mode,
            'address' => $address, // Dato útil a nivel raíz
            'portfolio' => $portfolio
        ];
    }

    /**
     * HELPER: Extrae saldos de una cadena específica
     */
    private function getBalancesByChainId($address, $chainId, $networkConfig)
    {
        $rpcUrl = $networkConfig['rpc_url'];

        $assets = [];

        // 1. Nativo
        $nativeHex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$address, 'latest']);
        $assets[$networkConfig['native_symbol']] = $this->hexToDec($nativeHex, 18);

        // 2. Tokens ERC-20
        $allTokens = config('zentrotraderbot.tokens');

        foreach ($allTokens as $symbol => $tokenData) {
            if ($tokenData['chain_id'] == $chainId && $tokenData['address'] !== '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee') {

                $tokenBalance = $this->getErc20Balance($rpcUrl, $tokenData['address'], $address, $tokenData['decimals']);

                // Opcional: Filtrar ceros si quieres limpiar la salida
                // if ($tokenBalance !== '0') {
                $assets[$symbol] = $tokenBalance;
                // }
            }
        }

        return [
            'network_id' => $chainId,
            'native_symbol' => $networkConfig['native_symbol'],
            'assets' => $assets
        ];
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
     * RETIRAR FONDOS (Híbrido Universal v0.9)
     * - Detecta EIP-1559 vs Legacy.
     * - Límites de gas seguros para contratos/exchanges.
     */
    public function withdraw(int $userId, string $toAddress, string $tokenSymbol, ?float $amount = null)
    {
        // NORMALIZACIÓN: Forzamos mayúsculas
        $tokenSymbol = strtoupper($tokenSymbol);

        // 1. OBTENER USUARIO
        try {
            $privateKey = $this->getDecryptedPrivateKey($userId);
            $fromAddress = $this->getAddressFromKey($privateKey); // Necesario para nonce y checks
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        // 2. CONFIGURACIÓN
        $tokenConfig = config("zentrotraderbot.tokens.$tokenSymbol");
        if (!$tokenConfig)
            return ['status' => 'error', 'message' => "Token no configurado."];

        $chainId = $tokenConfig['chain_id'];
        $network = config("zentrotraderbot.networks.$chainId");
        $rpcUrl = $network['rpc_url'];
        $isNative = $tokenConfig['address'] === '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee';

        Log::info("💸 Retiro v0.9: $tokenSymbol | Chain: $chainId");

        try {
            // 3. PREPARAR DATOS DE LA RED
            // Pedimos el bloque 'latest' completo para ver si tiene 'baseFeePerGas'
            $block = $this->rpcCall($rpcUrl, 'eth_getBlockByNumber', ['latest', false]);
            $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$fromAddress, 'pending']);
            if (!$nonceHex) {
                return ['status' => 'error', 'message' => 'Error de conexión: No se pudo obtener el Nonce de la red.'];
            }

            // ⚠️ AJUSTE GAS: 100k (Nativo) / 200k (Token) para evitar "Out of Gas" en contratos
            $gasLimitDec = $isNative ? 100000 : 200000;
            $gasLimitHex = $this->decToHex($gasLimitDec);

            // Variables de construcción
            $txType = '';
            $totalGasCostWei = '0';

            // Params específicos
            $p_gasPrice = null;
            $p_maxPriority = null;
            $p_maxFee = null;

            // ======================================================
            // 🧠 SELECCIÓN: ¿EIP-1559 O LEGACY?
            // ======================================================

            if (isset($block['baseFeePerGas'])) {
                // MODO MODERNO (Polygon, ETH, BSC Nuevo)
                $baseFeeDec = $this->hexToDecString($block['baseFeePerGas']);

                // Propina VIP: 35 Gwei
                $priorityFeeDec = bcmul('35', bcpow('10', '9'));

                // Max Fee = (Base * 2) + Propina
                $maxFeeDec = bcadd(bcmul($baseFeeDec, '2'), $priorityFeeDec);

                $p_maxPriority = $this->decToHex($priorityFeeDec);
                $p_maxFee = $this->decToHex($maxFeeDec);

                // Costo estimado máximo
                $totalGasCostWei = bcmul($maxFeeDec, (string) $gasLimitDec);
                $txType = 'EIP-1559';

            } else {
                // MODO LEGACY (Redes Viejas)
                $gasPriceHex = $this->rpcCall($rpcUrl, 'eth_gasPrice', []);
                $gasPriceDec = $this->hexToDecString($gasPriceHex);

                // Turbo Legacy: x1.5 del precio actual
                $turboGasPrice = bcmul($gasPriceDec, '1.5', 0);

                $p_gasPrice = $this->decToHex($turboGasPrice);

                $totalGasCostWei = bcmul($turboGasPrice, (string) $gasLimitDec);
                $txType = 'Legacy';
            }

            // ======================================================
            // 💰 CÁLCULOS DE MONTOS
            // ======================================================

            $finalTo = '';
            $finalValue = '0x0';
            $finalData = '';

            if ($isNative) {
                // NATIVO
                $balanceWei = $this->hexToDecString($this->rpcCall($rpcUrl, 'eth_getBalance', [$fromAddress, 'latest']));

                if ($amount === null) {
                    // MODO SWEEP: Restamos el costo del gas
                    $amountWei = bcsub($balanceWei, $totalGasCostWei);
                    if (bccomp($amountWei, '0') <= 0)
                        return ['status' => 'error', 'message' => "Falta gas ($txType)."];
                } else {
                    // MODO NORMAL
                    $amountWei = bcmul((string) $amount, bcpow('10', '18'));
                    if (bccomp(bcadd($amountWei, $totalGasCostWei), $balanceWei) > 0)
                        return ['status' => 'error', 'message' => 'Fondos insuficientes.'];
                }

                $finalTo = $toAddress;
                $finalValue = $this->decToHex($amountWei);

            } else {
                // ERC-20
                $nativeBal = $this->hexToDecString($this->rpcCall($rpcUrl, 'eth_getBalance', [$fromAddress, 'latest']));
                if (bccomp($nativeBal, $totalGasCostWei) < 0)
                    return ['status' => 'error', 'message' => "Falta gas nativo."];

                if ($amount === null) {
                    $amntTok = $this->getRawErc20Balance($rpcUrl, $tokenConfig['address'], $fromAddress);
                    if ($amntTok == '0')
                        return ['status' => 'error', 'message' => 'Sin saldo token.'];
                } else {
                    $amntTok = bcmul((string) $amount, bcpow('10', (string) $tokenConfig['decimals']));
                }

                $finalTo = $tokenConfig['address'];
                $finalData = '0xa9059cbb' . str_pad(substr($toAddress, 2), 64, '0', STR_PAD_LEFT) . str_pad($this->decToHexNoPrefix($amntTok), 64, '0', STR_PAD_LEFT);
            }

            // ======================================================
            // 🔨 CONSTRUCCIÓN MANUAL DE TX
            // ======================================================

            $tx = null;
            if ($txType === 'EIP-1559') {
                // Constructor v0.9
                $tx = new EIP1559Transaction($nonceHex, $p_maxPriority, $p_maxFee, $gasLimitHex, $finalTo, $finalValue, $finalData);
            } else {
                // Constructor v0.9
                $tx = new Transaction($nonceHex, $p_gasPrice, $gasLimitHex, $finalTo, $finalValue, $finalData);
            }

            $signedTx = $tx->getRaw($privateKey, $chainId);
            $txHash = $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx]);

            // VERIFICACIÓN ROBUSTA
            if (!$txHash) {
                // Si llegamos aquí, puede que la TX se haya enviado pero la RPC no respondió bien.
                // Lanzamos excepción para que el usuario sepa que algo raro pasó, 
                // pero ya no es un error de código "Undefined index".
                throw new \Exception("La transacción pudo haberse enviado, pero no recibimos confirmación del RPC. Revisa tu wallet.");
            }

            if (isset($txHash['error']))
                throw new \Exception("RPC Error: " . json_encode($txHash['error']));
            if (!$txHash)
                throw new \Exception("Error desconocido.");

            return [
                'status' => 'success',
                'tx_hash' => $txHash,
                'explorer' => $network['explorer'] . $txHash,
                'type' => $txType
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

    private function rpcCall($url, $method, $params)
    {
        try {
            $response = Http::post($url, [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => 1
            ]);

            $data = $response->json();

            // 1. Si hay error explícito en la RPC (ej: "insufficient funds")
            if (isset($data['error'])) {
                Log::warning("⚠️ RPC Error ($method): " . json_encode($data['error']));
                return null; // Retornamos null para manejarlo arriba
            }

            // 2. Intentamos obtener el resultado de forma segura
            return $data['result'] ?? null;

        } catch (\Exception $e) {
            // 3. Si falla la conexión HTTP (Timeout, DNS, etc)
            Log::error("❌ RPC HTTP Error ($method): " . $e->getMessage());
            return null;
        }
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