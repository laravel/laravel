<!DOCTYPE html>
<html>
<head>
    <title>View Store - Supermarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; margin-bottom: 30px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .nav-buttons { display: flex; gap: 10px; margin-bottom: 30px; }
        .nav-btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn-sell { background: #FF9800; color: white; }
        .btn-manage { background: #2196F3; color: white; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .product-card { background: #f9f9f9; padding: 20px; border-radius: 8px; border: 2px solid #ddd; }
        .product-card h3 { color: #333; margin-bottom: 10px; }
        .product-id { color: #666; font-size: 14px; margin-bottom: 10px; }
        .product-price { color: #4CAF50; font-size: 24px; font-weight: bold; margin: 10px 0; }
        .product-stock { color: #666; margin-top: 10px; }
        .in-stock { color: #4CAF50; }
        .low-stock { color: #FF9800; }
        .out-stock { color: #f44336; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #333; color: white; }
        tr:hover { background: #f5f5f5; }
        .total-info { background: #e3f2fd; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .total-info h3 { color: #1976D2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Store View</h1>

        <div class="nav-buttons">
            <a href="{{ route('selling') }}" class="nav-btn btn-sell">Back to Selling</a>
            <a href="{{ route('manage') }}" class="nav-btn btn-manage">Manage Inventory</a>
        </div>

        @if($products->count() > 0)
            <div class="total-info">
                <h3>Total Products: {{ $products->count() }}</h3>
                <p>Total Stock Value: LKR {{ number_format($products->sum(fn($p) => $p->price * $p->stock), 2) }}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Stock</th>
                        <th>Total Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>LKR {{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>LKR {{ number_format($product->price * $product->stock, 2) }}</td>
                        <td>
                            @if($product->stock > 10)
                                <span class="in-stock">✓ In Stock</span>
                            @elseif($product->stock > 0)
                                <span class="low-stock">⚠ Low Stock</span>
                            @else
                                <span class="out-stock">✗ Out of Stock</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No products in store. Add products in Manage Inventory.</p>
        @endif
    </div>
</body>
</html>
