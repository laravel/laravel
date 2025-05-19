<?php
namespace Modules\GutoTradeBot\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\GutoTradeBot\Entities\Accounts;

class AccountsSeeder extends Seeder
{
    public function run()
    {
        /*
        Accounts::create([
        'bank' => "Bizum",
        'name' => "Dayana Guerra Torres",
        'number' => "",
        'is_active' => true,
        "data" => array(
        "remain_operations" => 60,
        "number" => array(
        "+34614256154" => array(
        "bank" => "REVOLUT",
        "owners" => ["5219069448", "5919527201", "6211414111", "1256079990", "1269084609", "1705333263"], // KarimB99, Lixandro, Locol2023, TheSon_ofGod, AZOR79, chichifuentes
        ),
        ),
        ),
        ]);
        Accounts::create([
        'bank' => "Bizum",
        'name' => "Dayana Guerra Torres",
        'number' => "",
        'is_active' => true,
        "data" => array(
        "remain_operations" => 60,
        "number" => array(
        "+34643273368" => array(
        "bank" => "SABADELL",
        "owners" => ["5328142807", "873754229", "6549567189", "613173575", "347888105"], // EL_Lobo_DPEPDE, GermanDavid, Alej1961, Yander.ron, Arquimides
        ),
        ),
        ),
        ]);
        Accounts::create([
        'bank' => "Bizum",
        'name' => "Dayana Guerra Torres",
        'number' => "",
        'is_active' => false,
        "data" => array(
        "remain_operations" => 60,
        "number" => array(
        "+34643578515" => array(
        "bank" => "N26",
        //"owners" => [],
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "Bizum",
        'name' => "Dayami Proenza Pupo",
        'number' => "",
        'is_active' => true,
        "data" => array(
        "remain_operations" => 60,
        "number" => array(
        "+34651124638" => array(
        "bank" => "REVOLUT",
        "owners" => ["5508220560", "895670352", "1358852792", "1419502564", "1562139660", "1314081227"], // Deivys2000, GerardGames, Anibal, DrLimonta, EdutroLL, Jalvaro98
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "Bizum",
        'name' => "Dayami Proenza Pupo",
        'number' => "+34680739952",
        'is_active' => true,
        "data" => array(
        "remain_operations" => 60,
        ),
        ]);

        Accounts::create([
        'bank' => "Bizum",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "+34651122317",
        'is_active' => true,
        "data" => array(
        "remain_operations" => 30,
        ),
        ]);

        Accounts::create([
        'bank' => "Bizum",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "",
        'is_active' => false,
        "data" => array(
        "remain_operations" => 60,
        "number" => array(
        "+34676108331" => array(
        "bank" => "N26",
        //"owners" => [],
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "REVOLUT",
        'name' => "Dayana Guerra Torres",
        'number' => "ES48 1583 0001 1390 3095 0591",
        'detail' => "REVOESM2",
        'is_active' => false,
        ]);
        Accounts::create([
        'bank' => "N26",
        'name' => "Dayana Guerra Torres",
        'number' => "",
        'detail' => "NTSBESM1XXX",
        'is_active' => false,
        "data" => array(
        "number" => array(
        "ES16 1563 2626 3732 6692 7303" => array(
        //"owners" => [],
        ),
        ),
        ),
        ]);
        Accounts::create([
        'bank' => "SABADELL",
        'name' => "Dayana Guerra Torres",
        'number' => "",
        'detail' => "BSABESBB",
        'is_active' => true,
        "data" => array(
        "number" => array(
        "ES14 0081 2712 0500 0177 0287" => array(
        "owners" => ["6549567189", "5508220560", "1358852792", "6211414111", "1562139660", "5219069448"], // Alej1961, Deivys2000, Anibal, Locol2023, EdutroLL, KarimB99
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "REVOLUT",
        'name' => "Dayami Proenza Pupo",
        'number' => "",
        'detail' => "REVOESM2",
        'is_active' => true,
        "data" => array(
        "number" => array(
        "ES19 1583 0001 1790 2370 1816" => array(
        "owners" => ["5219069448", "873754229", "1269084609", "1256079990"], // KarimB99, GermanDavid, @AZOR79, TheSon_ofGod
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "N26",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "",
        'detail' => "NTSBESM1XXX",
        'is_active' => false,
        "data" => array(
        "number" => array(
        "ES36 1563 2626 3432 6818 5665" => array(
        //"owners" => [], //
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "Abanca",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "",
        'detail' => "CAGLESMMXXX",
        'is_active' => true,
        "data" => array(
        "number" => array(
        "ES57 2080 1011 0030 4003 8978" => array(
        "owners" => ["5328142807", "895670352", "1705333263", "1314081227"], // EL_Lobo_DPEPDE, GerardGames, chichifuentes, Jalvaro98
        ),
        ),
        ),
        ]);

        Accounts::create([
        'bank' => "FINOM",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "",
        'detail' => "FNOMESM2",
        'is_active' => true,
        "data" => array(
        "number" => array(
        "ES34 6726 8300 1307 1396 2670" => array(
        "owners" => ["613173575", "347888105", "1419502564"], // Yander.ron, Arquimides, DrLimonta
        ),
        ),
        "notes" => ["Esta cuenta puede que no admita transferencias instantáneas"],
        ),
        ]);

        Accounts::create([
        'bank' => "MONESE",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "BE14 9741 3628 2383",
        'detail' => "PESOBEB1",
        'is_active' => true,
        "data" => array(
        "notes" => ["Esta cuenta no admite transferencias instantáneas"],
        ),
        ]);

        Accounts::create([
        'bank' => "Paypal",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "rogerjoser87@gmail.com",
        'detail' => "https://www.paypal.me/rogerjoser",
        "data" => array(
        "images" => array(
        "/paypal_qr_roger.jpg",
        ),
        ),
        ]);
        Accounts::create([
        'bank' => "Paypal",
        'name' => "Dayami Proenza Pupo",
        'number' => "dproenzap@gmail.com",
        'detail' => "https://www.paypal.me/ProenzaPupo",
        "data" => array(
        "images" => array(
        "/paypal_qr_dayami.jpg",
        ),
        ),
        'is_active' => false,
        ]);

        Accounts::create([
        'bank' => "Western Union",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "Calle August 34 Prc 2 Tarragona, España",
        'detail' => "CP:43003 Telef:602081597",
        ]);

        Accounts::create([
        'bank' => "Western Union",
        'name' => "Dayami Proenza Pupo",
        'number' => "Calle August 34 Prc 2 Tarragona, España",
        'detail' => "CP:43003 NIE: Y9076594A",
        'is_active' => false,
        ]);

        Accounts::create([
        'bank' => "Western Union",
        'name' => "Mayra Pupo Batista",
        'number' => "Calle August 34 Prc 2 Tarragona, España",
        'detail' => "CP:43003 Pasaporte: M574617",
        'is_active' => false,
        ]);

        Accounts::create([
        'bank' => "SKRILL",
        'name' => "Roger Jose Ricardo Rodriguez",
        'number' => "rogerjoser87@gmail.com",
        'detail' => "+34 602081597",
        ]);

        Accounts::create([
        'bank' => "REVOLUT",
        'name' => "Dayami Proenza Pupo",
        'number' => "dproenzap@gmail.com",
        'detail' => "http://revolut.me/dayami",
        ]);

        Accounts::create([
        'bank' => "Caixabank",
        'name' => "Dayami Proenza Pupo",
        'number' => "ES38 2100 3510 7922 0073 9561",
        'detail' => "CAIXAESBBXXX",
        'is_active' => false,
        ]);

        Accounts::create([
        'bank' => "Qonto",
        'name' => "Dayami Proenza Pupo",
        'number' => "ES61 6888 0001 6474 9239 7895",
        'detail' => "QNTOESB2XXX",
        'is_active' => false,
        ]);
         

        Accounts::create([
            'bank' => "ING",
            'name' => "Dayami Proenza Pupo",
            'number' => "ES28 1465 0100 9617 6206 8204",
            'detail' => "INGDESMMXXX",
            'is_active' => true,
        ]);

        Accounts::create([
            'bank' => "Caixabank",
            'name' => "Dayami Proenza Pupo",
            'number' => "ES38 2100 3510 7922 0073 9561",
            'detail' => "CAIXAESBBXXX",
            'is_active' => true,
        ]);

        Accounts::create([
            'bank' => "Sabadell",
            'name' => "Dayami Proenza Pupo",
            'number' => "ES44 0081 2701 9700 0736 0846",
            'detail' => "BSABESBB",
            'is_active' => true,
        ]);

        Accounts::create([
            'bank' => "Santander",
            'name' => "Roger Jose Ricardo Rodriguez",
            'number' => "ES58 0049 5350 7222 1608 9066",
            'detail' => "BSCHESMMXXX",
            'is_active' => true,
        ]);

        Accounts::create([
            'bank' => "Ibercaja",
            'name' => "Roger Jose Ricardo Rodriguez",
            'number' => "ES46 2085 9507 8403 3062 6736",
            'detail' => "CAZRES2Z",
            'is_active' => true,
        ]);
        */


        Accounts::create([
            'bank' => "Modulr Finance, Ireland Branch",
            'name' => "Bridge Building Sp.z.o.o.",
            'number' => "IE11MODR99035506793800",
            'detail' => "MODRIE22XXX",
            'is_active' => true,
        ]);

        Accounts::create([
            'bank' => "Modulr Finance, Ireland Branch",
            'name' => "Bridge Building Sp.z.o.o.",
            'number' => "IE92MODR99035506405312",
            'detail' => "MODRIE22XXX",
            'is_active' => true,
        ]);


    }
}
