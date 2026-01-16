<?php
use App\Models\Product;

include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$products = Product::all();
foreach ($products as $product) {
    if ($product->hasVariants()) {
        $total = $product->variants()->where('is_active', true)->sum('stock_quantity');
        $product->update(['stock_quantity' => $total]);
        echo "Updated Product [{$product->id}] {$product->name}: Total stock = {$total}\n";
    }
}
echo "Stock synchronization completed.\n";
