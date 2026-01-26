<?php
namespace Modules\ZentroPackageBot\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ZentroPackageBot\Entities\Packages;
use Illuminate\Support\Str;

class PackagesSeeder extends Seeder
{

    public function run(): void
    {
        // 1. Paquetes con formato Internacional (UPU - como la primera foto)
        for ($i = 1; $i <= 5; $i++) {
            Packages::create([
                'tracking_number' => 'CM' . rand(100000000, 999999999) . 'AP',
                'internal_ref' => '175-' . rand(10000000, 99999999),
                'recipient_name' => $this->randomName(),
                'recipient_id' => $this->randomCI(),
                'recipient_phone' => '5' . rand(2000000, 9999999),
                'full_address' => 'Calle ' . rand(1, 100) . ' No. ' . rand(100, 500) . ' e/ ' . rand(1, 20) . ' y ' . rand(21, 40),
                'province' => 'Holguín',
                'destination_code' => 'HOG',
                'description' => 'Misceláneas y aseo',
                'weight_kg' => rand(5, 15) + (rand(0, 99) / 100),
                'status' => 'received',
                'sender_name' => 'Envío Internacional S.A.',
            ]);
        }

        // 2. Paquetes con formato Carga Aérea (AWB - como la segunda foto)
        $provincias = [
            ['name' => 'Santiago de Cuba', 'code' => 'SCU'],
            ['name' => 'La Habana', 'code' => 'HAV'],
            ['name' => 'Camagüey', 'code' => 'CMW']
        ];

        for ($i = 1; $i <= 5; $i++) {
            $prov = $provincias[array_rand($provincias)];
            Packages::create([
                'awb' => '996-' . rand(10000000, 99999999),
                'recipient_name' => $this->randomName(),
                'recipient_id' => $this->randomCI(),
                'recipient_phone' => '5' . rand(2000000, 9999999),
                'full_address' => 'Avenida Central No. ' . rand(10, 99),
                'province' => $prov['name'],
                'destination_code' => $prov['code'],
                'description' => $this->randomCargoItem(),
                'weight_kg' => rand(10, 50) + (rand(0, 99) / 100),
                'type' => 'no perecedero',
                'pieces' => 1,
                'sender_name' => 'Logística Global Italia',
                'sender_email' => 'info@logistica-global.it',
                'status' => 'in_transit',
            ]);
        }
    }

    // Auxiliares para generar datos realistas
    private function randomName()
    {
        $nombres = ['Onaidy', 'Yunier', 'Daily', 'Daniel', 'Beatriz', 'Carlos'];
        $apellidos = ['Gomez Duran', 'Rodriguez Sanchez', 'Perez Lopez', 'Hernandez'];
        return $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)];
    }

    private function randomCI()
    {
        // Genera un CI cubano plausible (YYMMDD + 5 dígitos)
        return rand(70, 99) . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT) . rand(10000, 99999);
    }

    private function randomCargoItem()
    {
        $items = ['GENERADOR DE CORRIENTE', 'SPLIT 12000 BTU', 'BATERÍA DE LITIO', 'NEUMÁTICOS'];
        return $items[array_rand($items)];
    }
}
