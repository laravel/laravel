<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetadataTypes;

class MetadataTypesSeeder extends DatabaseSeeder
{

    public function run(): void
    {
        MetadataTypes::create([
            'code' => 'metadata.system',
            'name' => 'Sistema',
            'comment' => 'Metadatos relacionados con el funcionamiento general del sistema',
        ]);
    }
}