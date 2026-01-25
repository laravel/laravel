<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory - Supermarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; margin-bottom: 30px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .nav-buttons { display: flex; gap: 10px; margin-bottom: 30px; }
        .nav-btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn-sell { background: #FF9800; color: white; }
        .btn-store { background: #4CAF50; color: white; }
        form { background: #f9f9f9; padding: 20px; margin-bottom: 30px; border-radius: 5px; }
        input, select { padding: 10px; margin: 5px; width: 200px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-add { background: #4CAF50; color: white; padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-remove { background: #f44336; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #333; color: white; }
        tr:hover { background: #f5f5f5; }
        .message { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Inventory</h1>

        <div class="nav-buttons">
            <a href="{{ route('selling') }}" class="nav-btn btn-sell">Back to Selling</a>
            <a href="{{ route('store') }}" class="nav-btn btn-store">View Store</a>
        </div>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <h3>Add New Product</h3>
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Unit Price" step="0.01" required>
            <input type="number" name="stock" placeholder="Quantity" required>
            <button type="submit" class="btn-add">Add Product</button>
        </form>

        <form method="POST" action="{{ route('products.updateStock') }}">
            @csrf
            @method('PUT')
            <h3>Add Stock by Product ID</h3>
            <select name="product_id" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">ID: {{ $product->id }} - {{ $product->name }} (Current: {{ $product->stock }})</option>
                @endforeach
            </select>
            <input type="number" name="quantity" placeholder="Quantity to Add" required min="1">
            <button type="submit" class="btn-add">Add Stock</button>
        </form>

        @if($products->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>LKR {{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline;padding:0;margin:0;background:none;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-remove" onclick="return confirm('Remove this product?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No products in inventory. Add your first product above!</p>
        @endif
    </div>
</body>
</html>
