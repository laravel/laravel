<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use kornrunner\Ethereum\Transaction;
use Elliptic\EC;
use kornrunner\Keccak;

class ZeroExController extends Controller
{
    // API V2 Oficial Global
    protected $zeroExBaseUrl = 'https://api.0x.org/swap/allowance-holder';

    /**
     * MÉTODO MAESTRO: SWAP
     * Ahora EXIGE la clave privada del usuario. El bot no tiene wallet propia.
     */
    public function swap(string $from, string $to, float $amount, string $userPrivateKey, $log = false)
    {
        set_time_limit(240);

        // 1. PREPARACIÓN DE CREDENCIALES
        // Limpiamos prefijo y derivamos la address pública para ser el 'taker'
        $activePrivateKey = str_replace('0x', '', $userPrivateKey);
        $activeWalletAddress = $this->deriveAddress($activePrivateKey);

        if ($log)
            Log::info("👤 Iniciando Swap para usuario: $activeWalletAddress");

        // 2. CARGA DE CONFIGURACIÓN
        $tokens = config('zentrotraderbot.tokens');
        if (!isset($tokens[$from]) || !isset($tokens[$to])) {
            throw new \Exception("Token no configurado: $from o $to");
        }

        // 3. DETECCIÓN DE RED
        $chainId = $tokens[$from]['chain_id'];
        if ($tokens[$to]['chain_id'] !== $chainId) {
            throw new \Exception("Error: Swap cruzado no soportado.");
        }

        $networkConfig = config("zentrotraderbot.networks.$chainId");
        $rpcUrl = $networkConfig['rpc_url'];
        $explorerUrl = $networkConfig['explorer'];

        // 4. DATOS DE TRANSACCIÓN
        $sellTokenAddress = $tokens[$from]['address'];
        $buyTokenAddress = $tokens[$to]['address'];
        $decimalsFactor = bcpow('10', (string) $tokens[$from]['decimals']);
        $amountInWei = bcmul((string) $amount, $decimalsFactor);

        if ($log)
            Log::info("🤖 [Bot] Swap en {$networkConfig['name']}: $amount $from -> $to");

        // 5. OBTENER QUOTE
        try {
            $quote = $this->getZeroExQuote($chainId, $sellTokenAddress, $buyTokenAddress, $amountInWei, $activeWalletAddress);
        } catch (\Exception $e) {
            Log::error("❌ Error Quote: " . $e->getMessage());
            throw $e;
        }

        // 6. AUTO-ALLOWANCE
        if (isset($quote['issues']['allowance'])) {
            $requiredSpender = $quote['issues']['allowance']['spender'];
            if ($log)
                Log::info("🛑 Falta permiso. Aprobando a: $requiredSpender...");

            $approveTxHash = $this->sendApproveTransaction(
                $rpcUrl,
                $chainId,
                $sellTokenAddress,
                $requiredSpender,
                $activePrivateKey,
                $activeWalletAddress,
                $log
            );

            if ($log)
                Log::info("📨 TX Approve enviada: $approveTxHash. Esperando...");

            if ($this->waitForConfirmation($rpcUrl, $approveTxHash, $log)) {
                if ($log)
                    Log::info("🔄 Aprobación confirmada. Reintentando Swap...");
                // RECURSIVIDAD: Pasamos la misma clave privada
                return $this->swap($from, $to, $amount, $userPrivateKey, $log);
            } else {
                throw new \Exception("Timeout aprobando token.");
            }
        }

        // 7. EJECUCIÓN
        if ($log)
            Log::info("✅ Permisos OK. Ejecutando Swap...");

        try {
            $txHash = $this->signAndSend($rpcUrl, $chainId, $quote, $activePrivateKey, $activeWalletAddress);

            if ($log)
                Log::info("🚀 Swap Enviado: $txHash");

            return [
                'status' => 'SWAPPED',
                'network' => $networkConfig['name'],
                'tx_hash' => $txHash,
                'explorer' => $explorerUrl . $txHash,
                'message' => "Operación completada: $amount $from -> $to"
            ];
        } catch (\Exception $e) {
            Log::error("❌ Error Crítico: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * DIAGNÓSTICO DE WALLET (Pública)
     * Ahora requiere que le pases la dirección a diagnosticar.
     */
    public function diagnoseWallet(string $walletAddress, string $tokenSymbol = 'USDC', $log = true)
    {
        $tokenConfig = config("zentrotraderbot.tokens.$tokenSymbol");

        if (!$tokenConfig)
            return response()->json(['error' => 'Token no encontrado'], 404);

        $chainId = $tokenConfig['chain_id'];
        $networkConfig = config("zentrotraderbot.networks.$chainId");
        $rpcUrl = $networkConfig['rpc_url'];

        if ($log)
            Log::info("🕵️‍♂️ Diagnóstico en {$networkConfig['name']} para $walletAddress");

        // Saldo Nativo
        $nativeBalanceHex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$walletAddress, 'latest']);
        $nativeBalance = hexdec($nativeBalanceHex) / 1e18;

        // Saldo Token
        $dataBalance = '0x70a08231' . str_pad(substr($walletAddress, 2), 64, '0', STR_PAD_LEFT);
        $tokenBalanceHex = $this->rpcCall($rpcUrl, 'eth_call', [['to' => $tokenConfig['address'], 'data' => $dataBalance], 'latest']);
        $tokenBalance = hexdec($tokenBalanceHex) / pow(10, $tokenConfig['decimals']);

        return response()->json([
            'network' => $networkConfig['name'],
            'wallet' => $walletAddress,
            'native_balance' => $nativeBalance . ' ' . $networkConfig['native_symbol'],
            'token_balance' => $tokenBalance . ' ' . $tokenSymbol,
        ]);
    }

    // ==========================================
    // MÉTODOS INTERNOS 
    // ==========================================

    private function deriveAddress($privateKey)
    {
        try {
            $ec = new EC('secp256k1');
            $keyPair = $ec->keyFromPrivate($privateKey);
            $pubKey = $keyPair->getPublic(false, 'hex');
            $hash = Keccak::hash(hex2bin(substr($pubKey, 2)), 256);
            return '0x' . substr($hash, -40);
        } catch (\Exception $e) {
            throw new \Exception("Clave privada inválida.");
        }
    }

    protected function getZeroExQuote($chainId, $sellTokenAddr, $buyTokenAddr, $amountWei, $takerAddress)
    {
        $apiUrl = $this->zeroExBaseUrl . "/quote";

        $params = [
            'chainId' => $chainId,
            'sellToken' => $sellTokenAddr,
            'buyToken' => $buyTokenAddr,
            'sellAmount' => $amountWei,
            'taker' => $takerAddress,
            'slippagePercentage' => '0.10'
        ];

        $response = Http::timeout(30)->retry(3, 1500)->withHeaders([
            '0x-api-key' => config('zentrotraderbot.0x_api_key'),
            '0x-version' => 'v2'
        ])->get($apiUrl, $params);

        if ($response->failed()) {
            $reason = $response->json()['reason'] ?? $response->body();
            throw new \Exception("0x API Error: $reason");
        }

        return $response->json();
    }

    protected function signAndSend($rpcUrl, $chainId, $quote, $privateKey, $walletAddress)
    {
        if (!isset($quote['transaction']))
            throw new \Exception("Respuesta 0x inválida");
        $txData = $quote['transaction'];

        $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$walletAddress, 'pending']);

        // Gas x2
        $estimatedGas = (string) $txData['gas'];
        $safeGasLimit = bcmul($estimatedGas, '2.0', 0);

        $tx = new Transaction(
            $nonceHex,
            $this->decToHex($txData['gasPrice']),
            $this->decToHex($safeGasLimit),
            $txData['to'],
            $this->decToHex($txData['value']),
            $txData['data']
        );

        $signedTx = $tx->getRaw($privateKey, $chainId);

        return $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx]);
    }

    protected function sendApproveTransaction($rpcUrl, $chainId, $tokenAddress, $spenderAddress, $privateKey, $walletAddress, $log = false)
    {
        $methodId = '0x095ea7b3'; // approve
        $data = $methodId . str_pad(substr($spenderAddress, 2), 64, '0', STR_PAD_LEFT) . str_repeat('f', 64);

        $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$walletAddress, 'pending']);

        // Gas Price Turbo (x2)
        $currentGasPriceHex = $this->rpcCall($rpcUrl, 'eth_gasPrice', []);
        $currentGasPriceDec = hexdec($currentGasPriceHex);
        $turboGasPrice = bcmul(number_format($currentGasPriceDec, 0, '.', ''), '2.0', 0);

        if ($log)
            Log::info("🔥 Modo Turbo ($chainId): Gas subido a $turboGasPrice Wei.");

        $tx = new Transaction($nonceHex, $this->decToHex($turboGasPrice), '0x186a0', $tokenAddress, '0x0', $data);

        $signedTx = $tx->getRaw($privateKey, $chainId);

        return $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx]);
    }

    protected function waitForConfirmation($rpcUrl, $txHash, $log = false)
    {
        if ($log)
            Log::info("⏳ Esperando TX: $txHash");
        $timeout = 120;
        $start = time();

        while (time() - $start < $timeout) {
            $receipt = $this->rpcCall($rpcUrl, 'eth_getTransactionReceipt', [$txHash]);

            if ($receipt) {
                if ($receipt['status'] === '0x1') {
                    if ($log)
                        Log::info("✅ TX Confirmada.");
                    return true;
                } else {
                    Log::error("❌ TX Falló (Reverted).");
                    return false;
                }
            }
            sleep(10); // Esperar 10 segundos antes de volver a preguntar
        }

        Log::error("⏰ Timeout esperando confirmación.");
        return false;
    }

    protected function rpcCall($url, $method, $params)
    {
        $response = Http::post($url, ['jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => 1]);
        if ($response->failed())
            throw new \Exception("RPC HTTP Error");
        return $response->json()['result'] ?? null;
    }

    protected function decToHex($decimal)
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
}