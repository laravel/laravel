<?php
use App\Models\Product;

$products = Product::all(['id', 'name', 'image_path', 'is_featured', 'is_active']);
foreach ($products as $p) {
    echo $p->id . ' | ' . $p->name . ' | is_featured=' . ($p->is_featured ? 'SIM' : 'NAO') . ' | is_active=' . ($p->is_active ? 'SIM' : 'NAO') . ' | path=' . $p->image_path . PHP_EOL;
}
