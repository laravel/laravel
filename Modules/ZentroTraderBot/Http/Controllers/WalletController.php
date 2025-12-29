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
     * CONSULTAR SALDO
     * Devuelve la dirección y enlaces para que el usuario verifique.
     */
    public function getBalance(int $userId)
    {
        // CORRECCIÓN: Buscamos en Actors, no en User
        $actor = Actors::where('user_id', $userId)->first();

        if (!$actor || !isset($actor->data['wallet']['address'])) {
            return ['error' => 'No tienes wallet configurada. Ejecuta /start.'];
        }

        $address = $actor->data['wallet']['address'];

        return [
            'address' => $address,
            'message' => 'Tus saldos en la Blockchain:',
            'polygon_scan' => "https://polygonscan.com/address/{$address}",
            'bsc_scan' => "https://bscscan.com/address/{$address}",
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
     * RETIRAR FONDOS (Token Nativo: POL/BNB)
     */
    public function withdraw(int $userId, string $toAddress, float $amount, string $tokenSymbol = 'POL')
    {
        try {
            // 1. Obtener Credenciales
            $privateKey = $this->getDecryptedPrivateKey($userId);

            // 2. Configuración de Red (Hardcoded a Polygon por ahora)
            // TODO: Hacer esto dinámico según el $tokenSymbol usando config('zentrotraderbot.tokens')
            $chainId = 137;
            $rpcUrl = config('zentrotraderbot.networks.137.rpc_url');

            Log::info("💸 Usuario $userId retirando $amount $tokenSymbol a $toAddress");

            // CASO A: Token Nativo
            if (in_array($tokenSymbol, ['POL', 'MATIC', 'BNB'])) {

                // Convertir a Wei (18 decimales)
                $amountWei = bcmul((string) $amount, bcpow('10', '18'));
                $amountHex = $this->decToHex($amountWei);

                // Preparar TX
                // Nota: getAddressFromKey es necesario para obtener el nonce correcto de ESTA wallet
                $myAddress = $this->getAddressFromKey($privateKey);
                $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$myAddress, 'pending']);

                $gasPriceHex = $this->rpcCall($rpcUrl, 'eth_gasPrice', []);
                $gasLimitHex = '0x5208'; // 21000 gas estándar

                $tx = new Transaction($nonceHex, $gasPriceHex, $gasLimitHex, $toAddress, $amountHex, '');

                // Firmar
                $signedTx = $tx->getRaw($privateKey, $chainId);

                // Enviar
                $txHash = $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx]);

                return ['status' => 'success', 'tx_hash' => $txHash, 'explorer' => "https://polygonscan.com/tx/$txHash"];
            }

            return ['status' => 'error', 'message' => 'Solo retiros nativos soportados por ahora.'];

        } catch (\Exception $e) {
            Log::error("❌ Error en retiro: " . $e->getMessage());
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