<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = Category::create([
            'name' => 'Especialidades da Casa',
            'slug' => 'especialidades-da-casa',
        ]);

        $items = [
            [
                'name' => 'Costela no Fogo de Chão',
                'price' => 89.00,
                'description' => 'Costela assada lentamente por 12 horas, acompanhada de mandioca derretendo, farofa crocante da casa e arroz soltinho.',
                'image_path' => 'images/food_1.png',
                'is_featured' => true,
            ],
            [
                'name' => 'Frango Caipira com Quiabo',
                'price' => 75.00,
                'description' => 'Frango criado solto, ensopado em molho rico e encorpado com quiabo da nossa horta, servido com angu suculento.',
                'image_path' => null,
                'is_featured' => true,
            ],
            [
                'name' => 'Tutu à Mineira Especial',
                'price' => 68.00,
                'description' => 'Feijão batido engrossado com farinha de milho, lombo suíno, linguiça artesanal, torresmo crocante e couve refogada.',
                'image_path' => null,
                'is_featured' => true,
            ],
        ];

        foreach ($items as $item) {
            Product::create([
                'category_id' => $especialidades->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'price' => $item['price'],
                'image_path' => $item['image_path'],
                'is_featured' => $item['is_featured'],
                'is_active' => true,
            ]);
        }
    }
}
