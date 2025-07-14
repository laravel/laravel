<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'Massachusetts Institute of Technology',
                'code' => 'MIT',
                'address' => '77 Massachusetts Ave, Cambridge, MA 02139, USA',
                'department_id' => 1
            ],
            [
                'name' => 'Stanford University',
                'code' => 'STAN',
                'address' => '450 Serra Mall, Stanford, CA 94305, USA',
                'department_id' => 1
            ],
            [
                'name' => 'California Institute of Technology',
                'code' => 'CALTECH',
                'address' => '1200 E California Blvd, Pasadena, CA 91125, USA',
                'department_id' => 1
            ],
            [
                'name' => 'University of California, Berkeley',
                'code' => 'UCB',
                'address' => 'Berkeley, CA 94720, USA',
                'department_id' => 1
            ],
            [
                'name' => 'Carnegie Mellon University',
                'code' => 'CMU',
                'address' => '5000 Forbes Ave, Pittsburgh, PA 15213, USA',
                'department_id' => 1
            ]
        ];

        foreach ($institutions as $institution) {
            DB::table('institutions')->insert([
                'name' => $institution['name'],
                'code' => $institution['code'],
                'address' => $institution['address'],
                'department_id' => $institution['department_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
