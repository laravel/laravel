<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Necesario para type hinting
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Necesario para rpcCall
use Elliptic\EC;
use kornrunner\Keccak;
use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;
use kornrunner\Ethereum\Transaction;
use kornrunner\Ethereum\EIP1559Transaction;
use Modules\Web3\Http\Controllers\WalletController;
use Modules\ZentroTraderBot\Entities\Suscriptions;

class TraderWalletController extends WalletController
{
    /**
     * Se llama cuando el usuario inicia el bot (/start).
     */
    public function getWallet(int $userId)
    {
        // 1. Buscar el Actor por su ID de Telegram (user_id)
        $suscriptor = Suscriptions::where('user_id', $userId)->first();

        // Si no existe el actor, retornamos error (o lo creamos según tu lógica)
        if (!$suscriptor) {
            // $suscriptor = Suscriptions::create(['user_id' => $userId, 'data' => []]); 
            return ['status' => 'error', 'message' => 'Usuario no registrado en el sistema.'];
        }

        $currentData = $suscriptor->data ?? [];

        // Validación: Si ya tiene wallet, devolvemos la existente
        if (isset($currentData['wallet']['address'])) {
            return [
                'status' => 'exists',
                'address' => $currentData['wallet']['address']
            ];
        }

        try {
            $wallet = $this->generateWallet();

            // 3. GUARDADO SEGURO EN JSON
            $walletData = [
                'address' => $wallet["address"],
                'private_key' => Crypt::encryptString($wallet["private_key"]), // 🔒 ENCRIPTADO
                'created_at' => now()->toIso8601String()
            ];

            $currentData['wallet'] = $walletData;
            $suscriptor->data = $currentData;
            $suscriptor->save();

            Log::info("✅ Wallet generada en JSON para usuario $userId");

            return ['status' => 'created', 'address' => $wallet["address"]];

        } catch (\Exception $e) {
            Log::error("❌ Error generando wallet: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error interno de criptografía'];
        }
    }

    /**
     * CONSULTAR SALDO (ESTANDARIZADO)
     * Siempre devuelve una estructura 'portfolio' consistente.
     */
    public function getBalance($userId, $networkSymbol = null)
    {
        // 1. Obtener Wallet
        $suscriptor = Suscriptions::where('user_id', $userId)->first();
        if (!$suscriptor || !isset($suscriptor->data['wallet']['address'])) {
            return ['status' => 'error', 'message' => 'No tienes wallet configurada.'];
        }
        $address = $suscriptor->data['wallet']['address'];

        return parent::getBalance($address, $networkSymbol);
    }

    /**
     * OBTENER CLAVE PRIVADA (DESCIFRADA)
     * Uso interno para firmar transacciones.
     */
    public function getDecryptedPrivateKey(int $userId)
    {
        $suscriptor = Suscriptions::where('user_id', $userId)->first();

        if (!$suscriptor || !isset($suscriptor->data['wallet']['private_key'])) {
            throw new \Exception("Usuario $userId no tiene wallet.");
        }

        $encryptedKey = $suscriptor->data['wallet']['private_key'];

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

        $privateKey = $this->getDecryptedPrivateKey($userId);

        return parent::withdraw($privateKey, $toAddress, $tokenSymbol, $amount);
    }
}