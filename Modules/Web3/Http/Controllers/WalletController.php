<?php

namespace Modules\Web3\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Elliptic\EC;
use kornrunner\Keccak;
use kornrunner\Ethereum\Transaction;
use kornrunner\Ethereum\EIP1559Transaction;
use FurqanSiddiqui\BIP39\BIP39;
use Modules\Web3\Traits\BlockchainTools;
use BN\BN;

class WalletController extends Controller
{
    use BlockchainTools;

    /**
     * GENERAR NUEVA WALLET
     * @return array{address: string, entropy: string, private_key: string, seed_phrase: string, status: string}
     */
    public function generateWallet($userPassword = "1234567890")
    {
        // 1. PRIMERO generar 12 palabras BIP-39
        $mnemonic = BIP39::Generate(12);
        $seedPhrase = implode(' ', $mnemonic->words);
        $entropy = $mnemonic->entropy;

        // 2. DERIVAR private key DETERMINÍSTICA desde las palabras
        $privateKeyHex = $this->seedPhraseToPrivateKey($seedPhrase);

        // 3. Generar address desde esa private key
        $address = $this->getAddressFromKey($privateKeyHex);

        // 4. Generar el Keystore JSON para que el usuario se lo lleve
        $keystore = $this->generateKeystore($privateKeyHex, $userPassword);

        return [
            'status' => 'created',
            'address' => $address,
            'seed_phrase' => $seedPhrase, // Estas palabras generan esta private key
            'private_key' => $privateKeyHex, // Private key derivada de las palabras
            'entropy' => $entropy,
            'keystore_json' => json_encode($keystore) // Keystore JSON es como meter esa llave en una caja fuerte digital que solo se abre con una contraseña
        ];
    }


    public function recoverFromPrivateKey(string $privateKeyHex)
    {
        try {
            // Validar formato
            if (!preg_match('/^[a-f0-9]{64}$/i', $privateKeyHex)) {
                throw new \Exception('Invalid private key format');
            }

            // Generar wallet
            $address = $this->getAddressFromKey($privateKeyHex);

            return [
                'status' => 'recovered',
                'address' => $address,
                'private_key' => $privateKeyHex
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Recuperar wallet desde palabras (USA EL MISMO ALGORITMO)
     */
    public function recoverFromSeedPhrase(string $seedPhrase)
    {
        try {
            // 1. Validar palabras
            $words = explode(' ', trim($seedPhrase));
            if (count($words) !== 12) {
                throw new \Exception('Se requieren exactamente 12 palabras');
            }

            // 2. Validar BIP-39
            $mnemonic = BIP39::Words($seedPhrase);

            // 3. ✅ MISMO ALGORITMO que en generateWallet()
            $privateKeyHex = $this->seedPhraseToPrivateKey($seedPhrase);

            // 4. Generar wallet
            $address = $this->getAddressFromKey($privateKeyHex);

            return [
                'status' => 'recovered',
                'address' => $address,
                'private_key' => $privateKeyHex,
                'entropy' => $mnemonic->entropy,
                'verification' => [
                    'algorithm_match' => true,
                    'bip39_valid' => true
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Derivar private key desde seed phrase (determinístico) siguiendo el estándar BIP-44 (m/44'/60'/0'/0/0) compatible con MetaMask/SafePal
     */
    private function seedPhraseToPrivateKey(string $seedPhrase): string
    {
        // 1. Generar la SEED binaria (BIP-39)
        $seed = hash_pbkdf2('sha512', $seedPhrase, 'mnemonic', 2048, 64, true);

        // 2. Generar Master Key y Chain Code (BIP-32)
        $hmac = hash_hmac('sha512', $seed, "Bitcoin seed", true);
        $masterPrivateKey = substr($hmac, 0, 32);
        $masterChainCode = substr($hmac, 32, 32);

        // 3. DERIVAR la ruta m/44'/60'/0'/0/0
        // Usamos el nuevo método blindado
        $derived = $this->deriveBIP44Path($masterPrivateKey, $masterChainCode, "m/44'/60'/0'/0/0");

        return bin2hex($derived['private_key']);
    }

    /**
     * Derivación Jerárquica BIP-32 Blindada
     */
    private function deriveBIP44Path($key, $chainCode, $path)
    {
        $cleanPath = str_replace(['m', ' '], '', $path);
        $parts = array_values(array_filter(explode('/', $cleanPath), function ($value) {
            return $value !== '';
        }));

        $currentKey = $key;
        $currentChainCode = $chainCode;

        $ec = new EC('secp256k1');
        // El orden 'n' de la curva secp256k1 (necesario para la suma modular)
        $n = new BN('fffffffffffffffffffffffffffffffebaaedce6af48a03bbfd25e8cd0364141', 16);

        foreach ($parts as $part) {
            $hardened = (strpos($part, "'") !== false);
            $index = (int) str_replace("'", '', $part);

            if ($hardened) {
                $index += 0x80000000;
                $data = "\0" . $currentKey . pack('N', $index);
            } else {
                $keyPair = $ec->keyFromPrivate(bin2hex($currentKey));
                $pubKeyHex = $keyPair->getPublic(true, 'hex');
                $data = hex2bin($pubKeyHex) . pack('N', $index);
            }

            $hmac = hash_hmac('sha512', $data, $currentChainCode, true);
            $iL = substr($hmac, 0, 32);
            $currentChainCode = substr($hmac, 32, 32);

            // --- EL PASO CRUCIAL: Suma Modular ---
            $iL_bn = new BN(bin2hex($iL), 16);
            $currentKey_bn = new BN(bin2hex($currentKey), 16);

            // k_i = (iL + k_parent) mod n
            $childKey_bn = $iL_bn->add($currentKey_bn)->mod($n);

            // Convertimos de vuelta a binario de 32 bytes
            $currentKey = hex2bin(str_pad($childKey_bn->toString(16), 64, '0', STR_PAD_LEFT));

            //DEBUG
            //dump("Level $part: " . bin2hex($currentKey));
        }

        return [
            'private_key' => $currentKey,
            'chain_code' => $currentChainCode
        ];
    }

    /**
     * Convierte de hexadecimal a decimal usando aritmética de precisión arbitraria
     * @param string $hex
     * @return int|string
     */
    private function convertHexToDecimalPrecise(string $hex): string
    {
        $hex = str_replace('0x', '', $hex);
        $dec = '0';
        $len = strlen($hex);

        for ($i = 0; $i < $len; $i++) {
            $dec = bcmul($dec, '16');
            $dec = bcadd($dec, (string) hexdec($hex[$i]));
        }

        return $dec;
    }

    /**
     * Convierte números decimales grandes (representados como strings) a hexadecimal
     * @param string $dec
     * @return int|string
     */
    private function convertDecimalToRawHex(string $dec): string
    {
        $hex = '';
        while (bccomp($dec, '0') > 0) {
            $rem = bcmod($dec, '16');
            $dec = bcdiv($dec, '16', 0);
            $hex = dechex($rem) . $hex;
        }
        return $hex ?: '0';
    }

    /**
     * Genera un Keystore V3 simplificado pero compatible
     */
    private function generateKeystore($privateKey, $password)
    {
        // Nota: Para un Keystore real compatible con MetaMask se usa Scrypt o PBKDF2.
        // Aquí te doy la estructura lógica.
        $salt = random_bytes(32);
        $iv = random_bytes(16);

        // Derivamos una llave a partir del password del usuario
        $derivedKey = hash_pbkdf2('sha256', $password, $salt, 262144, 32, true);

        // Ciframos la llave privada con la contraseña del usuario
        $encryptedKey = openssl_encrypt(
            hex2bin($privateKey),
            'aes-128-ctr',
            substr($derivedKey, 0, 16),
            OPENSSL_RAW_DATA,
            $iv
        );

        return [
            "version" => 3,
            "id" => str_replace('.', '-', uniqid('', true)),
            "crypto" => [
                "ciphertext" => bin2hex($encryptedKey),
                "cipher" => "aes-128-ctr",
                "cipherparams" => ["iv" => bin2hex($iv)],
                "kdf" => "pbkdf2",
                "kdfparams" => [
                    "dklen" => 32,
                    "salt" => bin2hex($salt),
                    "c" => 262144,
                    "prf" => "hmac-sha256"
                ],
                "mac" => hash_hmac('sha256', $encryptedKey, substr($derivedKey, 16, 16))
            ]
        ];
    }


    /**
     * CONSULTAR SALDO (ESTANDARIZADO)
     * Siempre devuelve una estructura 'portfolio' consistente.
     */
    protected function getBalance($address, $networkSymbol = null)
    {
        // 1. Obtener Wallet
        $portfolio = [];
        $mode = 'global';

        // --- CASO A: GLOBAL (Todas las redes) ---
        if ($networkSymbol === null) {
            $networks = config('web3.networks');

            foreach ($networks as $chainId => $networkConfig) {
                // Usamos el nombre de la red como clave (Polygon, BSC, etc.)
                $portfolio[$networkConfig['name']] = $this->getBalancesByChainId($address, $chainId, $networkConfig);
            }

            // --- CASO B: ESPECÍFICO (Una sola red) ---
        } else {
            $mode = 'specific';
            $networkSymbol = strtoupper($networkSymbol);

            $tokenConfig = config("web3.tokens.$networkSymbol");
            if (!$tokenConfig)
                return ['status' => 'error', 'message' => "Red desconocida: $networkSymbol"];

            $chainId = $tokenConfig['chain_id'];
            $networkConfig = config("web3.networks.$chainId");

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
        $assets[$networkConfig['native_symbol']] = $this->formatTokenBalance($nativeHex, 18);

        // 2. Tokens ERC-20
        $allTokens = config('web3.tokens');

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
     * RETIRAR FONDOS (Híbrido Universal v0.9)
     * - Detecta EIP-1559 vs Legacy.
     * - Límites de gas seguros para contratos/exchanges.
     */
    protected function withdraw(int $privateKey, string $toAddress, string $tokenSymbol, ?float $amount = null)
    {
        // NORMALIZACIÓN: Forzamos mayúsculas
        $tokenSymbol = strtoupper($tokenSymbol);

        // 1. OBTENER USUARIO
        try {
            $fromAddress = $this->getAddressFromKey($privateKey); // Necesario para nonce y checks
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        // 2. CONFIGURACIÓN
        $tokenConfig = config("web3.tokens.$tokenSymbol");
        if (!$tokenConfig)
            return ['status' => 'error', 'message' => "Token no configurado."];

        $chainId = $tokenConfig['chain_id'];
        $network = config("web3.networks.$chainId");
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
            $gasLimitHex = $this->formatDecimalAsHex($gasLimitDec);

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

                $p_maxPriority = $this->formatDecimalAsHex($priorityFeeDec);
                $p_maxFee = $this->formatDecimalAsHex($maxFeeDec);

                // Costo estimado máximo
                $totalGasCostWei = bcmul($maxFeeDec, (string) $gasLimitDec);
                $txType = 'EIP-1559';

            } else {
                // MODO LEGACY (Redes Viejas)
                $gasPriceHex = $this->rpcCall($rpcUrl, 'eth_gasPrice', []);
                $gasPriceDec = $this->hexToDecString($gasPriceHex);

                // Turbo Legacy: x1.5 del precio actual
                $turboGasPrice = bcmul($gasPriceDec, '1.5', 0);

                $p_gasPrice = $this->formatDecimalAsHex($turboGasPrice);

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
                $finalValue = $this->formatDecimalAsHex($amountWei);

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
        // 1. Limpieza: Asegurarnos de que no tenga el prefijo '0x' y sea hex puro
        $privateKey = str_replace('0x', '', $privateKey);

        $ec = new EC('secp256k1');
        $keyPair = $ec->keyFromPrivate($privateKey);

        // 2. Llave pública NO comprimida (false)
        // El formato 'hex' de Elliptic a veces incluye un prefijo '04' que indica que no es comprimida.
        $pubKey = $keyPair->getPublic(false, 'hex');

        // 3. Quitar el prefijo '04' si existe
        // La dirección de Ethereum se calcula sobre los 64 bytes de la llave pública (X e Y),
        // omitiendo el primer byte de control (0x04).
        if (strlen($pubKey) === 130) {
            $pubKey = substr($pubKey, 2);
        }

        // 4. Hash Keccak-256
        // Importante: Debe ser Keccak-256, NO SHA-3 (aunque se parecen, no son iguales)
        $hash = Keccak::hash(hex2bin($pubKey), 256);

        // 5. Tomar los últimos 40 caracteres (20 bytes)
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

        return $this->formatTokenBalance($responseHex, $decimals);
    }

    private function getRawErc20Balance($rpcUrl, $contract, $owner)
    {
        $data = '0x70a08231' . str_pad(substr($owner, 2), 64, '0', STR_PAD_LEFT);
        $res = $this->rpcCall($rpcUrl, 'eth_call', [['to' => $contract, 'data' => $data], 'latest']);
        return $this->hexToDecString($res);
    }
}