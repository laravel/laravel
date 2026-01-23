<?php

return [
    'name' => 'Web3',
    // --- REDES Y RPCs ---
    'networks' => [
        137 => [ // Polygon
            'name' => 'Polygon',
            'rpc_url' => 'https://polygon-rpc.com', // O tu RPC privado
            'native_symbol' => 'POL', // o MATIC
            'explorer' => 'https://polygonscan.com/tx/'
        ],
        56 => [ // Binance Smart Chain (BSC)
            'name' => 'BSC',
            'rpc_url' => 'https://bsc-dataseed.binance.org/',
            'native_symbol' => 'BNB',
            'explorer' => 'https://bscscan.com/tx/'
        ],
    ],
    // --- TOKENS (Agrupados por clave única) ---
    'tokens' => [
        // --- POLYGON ---
        'POL' => [
            'address' => '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', // Token Nativo 0x
            'decimals' => 18,
            'chain_id' => 137,
        ],
        'USDC' => [ // Polygon USDC
            'address' => '0x3c499c542cef5e3811e1192ce70d8cc03d5c3359', // Native USDC
            'decimals' => 6,
            'chain_id' => 137,
        ],

        // --- BSC (Binance Smart Chain) ---
        'BNB' => [
            'address' => '0xeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', // Token Nativo 0x
            'decimals' => 18,
            'chain_id' => 56,
        ],
        'USDT' => [ // BSC USDT (BEP20)
            'address' => '0x55d398326f99059ff775485246999027b3197955', // USDT en BSC
            'decimals' => 18,
            'chain_id' => 56,
        ]
    ],
];

