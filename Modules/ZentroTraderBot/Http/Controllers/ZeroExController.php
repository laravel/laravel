<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use kornrunner\Ethereum\Transaction;
use kornrunner\Ethereum\EIP1559Transaction;
use Elliptic\EC;
use kornrunner\Keccak;
use Modules\ZentroTraderBot\Traits\BlockchainTools;

class ZeroExController extends Controller
{
    use BlockchainTools;

    // API V2 Oficial Global
    protected $zeroExBaseUrl = 'https://api.0x.org/swap/allowance-holder';

    /**
     * MÉTODO MAESTRO: SWAP
     */
    public function swap(string $from, string $to, float $amount, string $userPrivateKey, $nofifyFn, $log = false)
    {
        set_time_limit(300);

        $from = strtoupper($from);
        $to = strtoupper($to);

        // 1. PREPARACIÓN DE CREDENCIALES
        // Limpiamos prefijo y derivamos la address pública para ser el 'taker'
        $activePrivateKey = str_replace('0x', '', $userPrivateKey);
        $activeWalletAddress = $this->deriveAddress($activePrivateKey);

        if ($log)
            Log::info("👤 Iniciando Swap para usuario: $activeWalletAddress: $amount $from -> $to");

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

        // -----------------------------------------------------
        // 📸 SNAPSHOT 1: SALDO ANTES (Del token que vamos a recibir)
        // -----------------------------------------------------
        $balanceBefore = $this->getLiveBalance($rpcUrl, $buyTokenAddress, $activeWalletAddress, $tokens[$to]['decimals']);

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

            $text = "🛑 *Falta permiso*. _Aprobando a_: `$requiredSpender`...";
            $nofifyFn($text, 1);
            if ($log)
                Log::info($text);

            $approveTxHash = $this->sendApproveTransaction(
                $rpcUrl,
                $chainId,
                $sellTokenAddress,
                $requiredSpender,
                $activePrivateKey,
                $activeWalletAddress,
                $log
            );

            $text = "📨 _TX Approve enviada:_ `$approveTxHash`. _Esperando confirmacion..._";
            $nofifyFn($text, 1);
            if ($log)
                Log::info($text);

            if ($this->waitForConfirmation($rpcUrl, $approveTxHash, $nofifyFn, $log)) {
                $text = "🔄 *Aprobación confirmada*. _Reintentando Swap..._";
                $nofifyFn($text, 1);
                if ($log)
                    Log::info($text);

                // RECURSIVIDAD: Pasamos la misma clave privada
                return $this->swap($from, $to, $amount, $userPrivateKey, $nofifyFn, $log);
            } else {
                throw new \Exception("Timeout aprobando token.");
            }
        }
        if ($log)
            Log::info("✅ Permisos OK. Ejecutando Swap...");

        // 7. EJECUCIÓN DEL SWAP
        try {
            $txHash = $this->signAndSend($rpcUrl, $chainId, $quote, $activePrivateKey, $activeWalletAddress);

            $text = "⏳ _TX Enviada:_ `$txHash`";
            $nofifyFn($text, 1);
            if ($log)
                Log::info($text);

            // 5. ⏳ ESPERAR CONFIRMACIÓN (Mining...)
            $confirmed = $this->waitForConfirmation($rpcUrl, $txHash, $nofifyFn, $log);

            if (!$confirmed) {
                throw new \Exception("La transacción se envió pero no se confirmó a tiempo.");
            }

            // -----------------------------------------------------
            // 📸 SNAPSHOT 2: SALDO DESPUÉS
            // -----------------------------------------------------
            // Pequeña pausa de seguridad por si el nodo RPC tiene lag de indexado
            sleep(2);
            $balanceAfter = $this->getLiveBalance($rpcUrl, $buyTokenAddress, $activeWalletAddress, $tokens[$to]['decimals']);

            // 6. CÁLCULO FINAL
            $receivedAmount = $balanceAfter - $balanceBefore;

            // Corrección de negativos por error de redondeo o gas en nativos
            if ($receivedAmount < 0)
                $receivedAmount = 0;


            $text = "🎉 *SWAP EXITOSO*. Recibidos: +$receivedAmount $to";
            $nofifyFn($text, 1);
            if ($log)
                Log::info($text);

            return [
                'status' => 'SWAPPED',
                'network' => $networkConfig['name'],
                'tx_hash' => $txHash,
                'explorer' => $explorerUrl . $txHash,
                'amount_received' => (float) $receivedAmount,
                'message' => "Operación completada: $amount $from -> $to"
            ];
        } catch (\Exception $e) {
            Log::error("❌ Error Crítico: " . $e->getMessage());
            throw $e;
        }
    }

    // --- HELPER PRIVADO PARA LEER SALDO RÁPIDO ---
    private function getLiveBalance($rpcUrl, $tokenAddress, $walletAddress, $decimals)
    {
        // Caso Nativo (0xeeee...)
        if ($tokenAddress === '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee') {
            $hex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$walletAddress, 'latest']);
        } else {
            // Caso ERC-20
            $data = '0x70a08231' . str_pad(substr($walletAddress, 2), 64, '0', STR_PAD_LEFT);
            $hex = $this->rpcCall($rpcUrl, 'eth_call', [['to' => $tokenAddress, 'data' => $data], 'latest']);
        }

        // Usamos el Trait para convertir
        // Nota: hexToDec devuelve string formateado, hacemos cast a float para restar matemáticamente
        return (float) $this->hexToDec($hex, $decimals);
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
        $nativeBalanceHex = $this->rpcCall($rpcUrl, 'eth_getBalance', [$walletAddress, 'latest'], true);
        $nativeBalance = hexdec($nativeBalanceHex) / 1e18;

        // Saldo Token
        $dataBalance = '0x70a08231' . str_pad(substr($walletAddress, 2), 64, '0', STR_PAD_LEFT);
        $tokenBalanceHex = $this->rpcCall($rpcUrl, 'eth_call', [['to' => $tokenConfig['address'], 'data' => $dataBalance], 'latest'], true);
        $tokenBalance = hexdec($tokenBalanceHex) / pow(10, $tokenConfig['decimals']);

        return response()->json([
            'network' => $networkConfig['name'],
            'wallet' => $walletAddress,
            'native_balance' => $nativeBalance . ' ' . $networkConfig['native_symbol'],
            'token_balance' => $tokenBalance . ' ' . $tokenSymbol,
        ]);
    }

    // ==========================================
    // MÉTODOS INTERNOS BLINDADOS (EIP-1559)
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
        // Nota: Asegúrate de que zentrotraderbot.0x_api_key está en tu config/
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

    // 🔥 MEJORADO: Soporta EIP-1559
    protected function signAndSend($rpcUrl, $chainId, $quote, $privateKey, $walletAddress)
    {
        if (!isset($quote['transaction']))
            throw new \Exception("Respuesta 0x inválida");
        $txData = $quote['transaction'];

        $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$walletAddress, 'pending'], true);

        // 1. Gas Limit (Seguro x1.5)
        $estimatedGas = (string) $txData['gas'];
        $safeGasLimit = bcmul($estimatedGas, '1.5', 0);
        $gasLimitHex = $this->decToHex($safeGasLimit);

        // 2. ¿La red soporta EIP-1559?
        $block = $this->rpcCall($rpcUrl, 'eth_getBlockByNumber', ['latest', false], true);

        if (isset($block['baseFeePerGas'])) {
            // --- MODO MODERNO (Polygon/ETH) ---
            $baseFeeDec = $this->hexToDecString($block['baseFeePerGas']);
            $priorityFeeDec = bcmul('35', bcpow('10', '9')); // 35 Gwei propina
            $maxFeeDec = bcadd(bcmul($baseFeeDec, '2'), $priorityFeeDec); // Base x2 + Propina

            $tx = new EIP1559Transaction(
                $nonceHex,
                $this->decToHex($priorityFeeDec),
                $this->decToHex($maxFeeDec),
                $gasLimitHex,
                $txData['to'],
                $this->decToHex($txData['value']),
                $txData['data']
            );
        } else {
            // --- MODO LEGACY (BSC Viejo) ---
            $gasPriceHex = $this->decToHex($txData['gasPrice']); // Usamos el precio que sugiere 0x
            $tx = new Transaction(
                $nonceHex,
                $gasPriceHex,
                $gasLimitHex,
                $txData['to'],
                $this->decToHex($txData['value']),
                $txData['data']
            );
        }

        $signedTx = $tx->getRaw($privateKey, $chainId);
        return $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx], true);
    }

    // 🔥 MEJORADO: Soporta EIP-1559 para Approve
    protected function sendApproveTransaction($rpcUrl, $chainId, $tokenAddress, $spenderAddress, $privateKey, $walletAddress, $log = false)
    {
        $methodId = '0x095ea7b3'; // approve
        $data = $methodId . str_pad(substr($spenderAddress, 2), 64, '0', STR_PAD_LEFT) . str_repeat('f', 64);

        $nonceHex = $this->rpcCall($rpcUrl, 'eth_getTransactionCount', [$walletAddress, 'pending'], true);
        $gasLimitHex = '0x186a0'; // 100,000 gas fijo para approve

        $block = $this->rpcCall($rpcUrl, 'eth_getBlockByNumber', ['latest', false], true);

        if (isset($block['baseFeePerGas'])) {
            // EIP-1559
            $baseFeeDec = $this->hexToDecString($block['baseFeePerGas']);
            $priorityFeeDec = bcmul('35', bcpow('10', '9'));
            $maxFeeDec = bcadd(bcmul($baseFeeDec, '2'), $priorityFeeDec);

            $tx = new EIP1559Transaction(
                $nonceHex,
                $this->decToHex($priorityFeeDec),
                $this->decToHex($maxFeeDec),
                $gasLimitHex,
                $tokenAddress,
                '0x0',
                $data
            );
        } else {
            // Legacy Turbo
            $currentGasPriceHex = $this->rpcCall($rpcUrl, 'eth_gasPrice', [], true);
            $currentGasPriceDec = hexdec($currentGasPriceHex);
            $turboGasPrice = bcmul(number_format($currentGasPriceDec, 0, '.', ''), '2.0', 0);

            if ($log)
                Log::info("🔥 Modo Turbo ($chainId): Gas subido a $turboGasPrice Wei.");

            $tx = new Transaction($nonceHex, $this->decToHex($turboGasPrice), $gasLimitHex, $tokenAddress, '0x0', $data);
        }

        $signedTx = $tx->getRaw($privateKey, $chainId);
        return $this->rpcCall($rpcUrl, 'eth_sendRawTransaction', ['0x' . $signedTx], true);
    }

    protected function waitForConfirmation($rpcUrl, $txHash, $nofifyFn, $log = false)
    {
        $timeout = 120;
        $start = time();
        while (time() - $start < $timeout) {
            $receipt = $this->rpcCall($rpcUrl, 'eth_getTransactionReceipt', [$txHash], true);
            if ($receipt) {
                if ($receipt['status'] === '0x1') {
                    $text = "✅ *TX Confirmada* `$txHash`.";
                    $nofifyFn($text, 1);
                    if ($log)
                        Log::info($text);
                    return true;
                } else {
                    $text = "❌ *TX Fallo* _(Reverted)_.";
                    $nofifyFn($text, 1);
                    if ($log)
                        Log::info($text);
                    return false;
                }
            }
            sleep(10); // Esperar 10 segundos antes de volver a preguntar
        }
        Log::error("⏰ Timeout esperando confirmación.");
        return false;
    }
}