<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisCuti;

class JenisCutiSeeder extends Seeder
{
    public function run(): void
    {
        $jenisCuti = [
            ['nama' => 'Cuti Tahunan', 'max_hari' => 12, 'keterangan' => 'Cuti tahunan PNS'],
            ['nama' => 'Cuti Besar', 'max_hari' => 90, 'keterangan' => 'Cuti besar setiap 6 tahun sekali'],
            ['nama' => 'Cuti Sakit', 'max_hari' => 365, 'keterangan' => 'Cuti karena sakit'],
            ['nama' => 'Cuti Melahirkan', 'max_hari' => 90, 'keterangan' => 'Cuti bagi PNS perempuan yang melahirkan'],
            ['nama' => 'Cuti Alasan Penting', 'max_hari' => 60, 'keterangan' => 'Cuti karena alasan penting'],
            ['nama' => 'Cuti di Luar Tanggungan Negara', 'max_hari' => 1095, 'keterangan' => 'Cuti di luar tanggungan negara (max 3 tahun)'],
        ];

        foreach ($jenisCuti as $jc) {
            JenisCuti::create($jc);
        }
    }
}
