<?php

namespace Faker\Provider\pt_BR;

class Payment extends \Faker\Provider\Payment
{
    protected static $cardVendors = [
        'Visa', 'Visa', 'Visa', 'Visa', 'Visa',
        'MasterCard', 'MasterCard', 'MasterCard', 'MasterCard', 'MasterCard',
        'American Express', 'Discover Card', 'Diners', 'Elo', 'Hipercard',
    ];

    // see https://gist.github.com/erikhenrique/5931368 / http://pt.stackoverflow.com/q/3715/26461
    protected static $cardParams = [
        'Visa' => [
            '4##############',
        ],
        'MasterCard' => [
            '5##############',
        ],
        'American Express' => [
            '34############',
            '37############',
        ],
        'Discover Card' => [
            '6011###########',
            '622############',
            '64#############',
            '65#############',
        ],
        'Diners' => [
            '301############',
            '301##########',
            '305############',
            '305##########',
            '36#############',
            '36###########',
            '38#############',
            '38###########',
        ],
        'Elo' => [
            '636368#########',
            '438935#########',
            '504175#########',
            '451416#########',
            '636297#########',
            '5067###########',
            '4576###########',
            '4011###########',
        ],
        'Hipercard' => [
            '38#############',
            '60#############',
        ],
        'Aura' => [
            '50#############',
        ],
    ];

    /**
     * International Bank Account Number (IBAN)
     *
     * @see http://en.wikipedia.org/wiki/International_Bank_Account_Number
     *
     * @param string $prefix      for generating bank account number of a specific bank
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @param int    $length      total length without country code and 2 check digits
     *
     * @return string
     */
    public static function bankAccountNumber($prefix = '', $countryCode = 'BR', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }

    /**
     * @see list of Brazilians banks (2018-02-15), source: https://pt.wikipedia.org/wiki/Lista_de_bancos_do_Brasil
     */
    protected static $banks = [
        'BADESUL Desenvolvimento S.A. – Agência de Fomento/RS',
        'Banco Central do Brasil',
        'Banco da Amazônia',
        'Banco de Brasília',
        'Banco de Desenvolvimento de Minas Gerais',
        'Banco de Desenvolvimento do Espírito Santo',
        'Banco de Desenvolvimento do Paraná',
        'Banco do Brasil',
        'Banco do Estado de Sergipe	Banese	Estadual',
        'Banco do Estado do Espírito Santo	Banestes',
        'Banco do Estado do Pará',
        'Banco do Estado do Rio Grande do Sul',
        'Banco do Nordeste do Brasil',
        'Banco Nacional de Desenvolvimento Econômico e Social',
        'Banco Regional de Desenvolvimento do Extremo Sul',
        'Caixa Econômica Federal',
        'Banco ABN Amro S.A.',
        'Banco Alfa',
        'Banco Banif',
        'Banco BBM',
        'Banco BMG',
        'Banco Bonsucesso',
        'Banco BTG Pactual',
        'Banco Cacique',
        'Banco Caixa Geral - Brasil',
        'Banco Citibank',
        'Banco Credibel',
        'Banco Credit Suisse',
        'Góis Monteiro & Co',
        'Banco Fator',
        'Banco Fibra',
        'Agibank',
        'Banco Guanabara',
        'Banco Industrial do Brasil',
        'Banco Industrial e Comercial',
        'Banco Indusval',
        'Banco Inter',
        'Banco Itaú BBA',
        'Banco ItaúBank',
        'Banco Itaucred Financiamentos',
        'Banco Mercantil do Brasil',
        'Banco Modal	Modal',
        'Banco Morada',
        'Banco Pan',
        'Banco Paulista',
        'Banco Pine',
        'Banco Renner',
        'Banco Ribeirão Preto',
        'Banco Safra',
        'Banco Santander',
        'Banco Sofisa',
        'Banco Topázio',
        'Banco Votorantim',
        'Bradesco Bradesco',
        'Itaú Unibanco',
        'Banco Original',
        'Banco Neon',
        'Nu Pagamentos S.A',
        'XP Investimentos Corretora de Câmbio Títulos e Valores Mobiliários S.A',
    ];

    /**
     * @example 'Banco Neon'
     */
    public static function bank()
    {
        return static::randomElement(static::$banks);
    }
}
