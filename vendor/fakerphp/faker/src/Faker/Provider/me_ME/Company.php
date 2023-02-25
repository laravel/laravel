<?php

namespace Faker\Provider\me_ME;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companyName}} {{companyType}}',
    ];

    /**
     * Source: extracted from http://www.crps.me/index.php/predraga
     */
    protected static $names = [
        '13 Jul - Plantaže ', '19 Decembar Podgorica',
        'Agrokombinat 13 Jul', 'Agrokombinat 13 Jul', 'Atlas Banka', 'Autoremont Osmanagić',
        'Božur-Velexport', 'Businessmontenegro Podgorica',
        'Cemex Montenegro', 'Centralna Depozitarna Agencija ', 'Centrokoža-Produkt ', 'CG Broker', 'CMC AD Podgorica', 'Crnagoradrvo', 'Crnagoraput', 'Crnogorska Komercijalna Banka ', 'Crnogorski Telekom', 'Doclea Express Podgorica', 'Društvo Za Upravljanje Investicionim Fondom Atlas Mont', 'Drvoimpex', 'Drvoimpex', 'Drvoimpex', 'Drvoimpex-Bams', 'Drvoimpex-Fincom', 'Drvoimpex-Gm Podgorica', 'Drvoimpex-Mobile', 'Duklja - Zora', 'Duklja Podgorica', 'Duklja-Pekara', 'Duvanski Kombinat, Podgorica',
        'Elastik-Plastika Ad Podgorica', 'Erste Bank Ad Podgorica', 'Euromarket Banka Nlb Grupa ', 'Exal', 'Export-Import Servisimport Si',
        'Fond Zajedničkog Ulaganja Moneta', 'Goricapromet Podgorica', 'Gornji Ibar A.D.', 'Gp Radnik Beton', 'Građevinar Podgorica', 'Gross Market Podgorica',
        'H.T.P.Velika Plaža A.D', 'Higijena Podgorica', 'Hipotekarna Banka', 'Hotel Ravnjak',
        'Industriaimpex A.D.', 'Inpek', 'Institut Za Šumarstvo ', 'Intours', 'Invest Banka Montenegro', 'Izdavačko-Prometno Društvo Ljetopis',
        'Lovćen Podgorica', 'Lovćen-Re', 'Lovćeninvest Podgorica', 'Lutrija Crne Gore',
        'Margomarket Podgorica', 'Mašinopromet', 'Mašinopromet-Commerce', 'Mesopromet Podgorica', 'Mljekara', 'Moneta A.D.', 'Montenegroberza Akcionarsko Društvo', 'Morača Podgorica', 'Morača Sa P.O.',
        'Nex Montenegro', 'Novogradnja Ad Podgorica',
        'Osiguravajuće Društvo Swiss Osiguranje',
        'Papir', 'Podgoricaekspres', 'Progas Podgorica', 'Promet', 'Prva Banka Crne Gore Podgorica',
        'Ribnica Commerce',
        'Sava Montenegro Podgorica', 'Si Promet A.D.', 'Sigmobil', 'Societe Generale Banka Montenegro Ad', 'Solar 80 - Elastik', 'Stadion', 'Šumarsko Preduzeće ',
        'Tehnomarketi', 'Tpc Ražnatović', 'Trend A.D.', 'Trgopress',
        'Unifarm ', 'Utip Crna Gora',
        'Vatrostalna Podgorica', 'Velepromet Podgorica', 'Veletrgovina-Kolašin', 'Velimport Podgorica', 'Volumentrade Podgorica',
        'Željeznica Crne Gore', 'Zetatrans',
    ];

    protected static $types = [
        'A.D.', 'A.D PODGORICA',
    ];

    public static function companyType()
    {
        return static::randomElement(static::$types);
    }

    public static function companyName()
    {
        return static::randomElement(static::$names);
    }
}
