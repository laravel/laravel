<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@arcanepay.biz.id'],
            [
                'name'     => 'Admin ArcanePay',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'GantiPassword123!')),
            ]
        );

        // ── Game Categories ───────────────────────────────────────────────────
        $games = [
            [
                'name'         => 'Mobile Legends',
                'slug'         => 'mobile-legends',
                'icon'         => '⚔️',
                'status'       => true,
                'need_zone'    => true,
                'zone_label'   => 'Server ID',
                'target_label' => 'User ID',
            ],
            [
                'name'         => 'Free Fire',
                'slug'         => 'free-fire',
                'icon'         => '🔥',
                'status'       => true,
                'need_zone'    => false,
                'zone_label'   => null,
                'target_label' => 'User ID',
            ],
            [
                'name'         => 'PUBG Mobile',
                'slug'         => 'pubg-mobile',
                'icon'         => '🎯',
                'status'       => true,
                'need_zone'    => false,
                'zone_label'   => null,
                'target_label' => 'Player ID',
            ],
            [
                'name'         => 'Genshin Impact',
                'slug'         => 'genshin-impact',
                'icon'         => '🌟',
                'status'       => true,
                'need_zone'    => true,
                'zone_label'   => 'Server',
                'target_label' => 'UID',
            ],
            [
                'name'         => 'Honkai Star Rail',
                'slug'         => 'honkai-star-rail',
                'icon'         => '⭐',
                'status'       => true,
                'need_zone'    => true,
                'zone_label'   => 'Server',
                'target_label' => 'UID',
            ],
        ];

        foreach ($games as $game) {
            Category::firstOrCreate(['slug' => $game['slug']], $game);
        }

        // ── Sample Products ───────────────────────────────────────────────────
        // NOTE: Ganti supplier_code dengan kode dari dashboard Digiflazz
        //       setelah akun aktif.

        $ml = Category::where('slug', 'mobile-legends')->first();
        if ($ml) {
            $mlProducts = [
                ['name' => '86 Diamonds', 'supplier_code' => 'mobile-legends-86-diamonds', 'base_price' => 19000, 'sell_price' => 22000],
                ['name' => '172 Diamonds', 'supplier_code' => 'mobile-legends-172-diamonds', 'base_price' => 38000, 'sell_price' => 43000],
                ['name' => '257 Diamonds', 'supplier_code' => 'mobile-legends-257-diamonds', 'base_price' => 55000, 'sell_price' => 62000],
                ['name' => '344 Diamonds', 'supplier_code' => 'mobile-legends-344-diamonds', 'base_price' => 73000, 'sell_price' => 82000],
                ['name' => '514 Diamonds', 'supplier_code' => 'mobile-legends-514-diamonds', 'base_price' => 108000, 'sell_price' => 120000],
            ];
            foreach ($mlProducts as $p) {
                Product::firstOrCreate(
                    ['category_id' => $ml->id, 'name' => $p['name']],
                    [...$p, 'category_id' => $ml->id, 'status' => true]
                );
            }
        }

        $ff = Category::where('slug', 'free-fire')->first();
        if ($ff) {
            $ffProducts = [
                ['name' => '70 Diamonds', 'supplier_code' => 'free-fire-70-diamonds', 'base_price' => 15000, 'sell_price' => 17000],
                ['name' => '140 Diamonds', 'supplier_code' => 'free-fire-140-diamonds', 'base_price' => 29000, 'sell_price' => 33000],
                ['name' => '355 Diamonds', 'supplier_code' => 'free-fire-355-diamonds', 'base_price' => 72000, 'sell_price' => 82000],
                ['name' => '720 Diamonds', 'supplier_code' => 'free-fire-720-diamonds', 'base_price' => 143000, 'sell_price' => 160000],
            ];
            foreach ($ffProducts as $p) {
                Product::firstOrCreate(
                    ['category_id' => $ff->id, 'name' => $p['name']],
                    [...$p, 'category_id' => $ff->id, 'status' => true]
                );
            }
        }
    }
}
