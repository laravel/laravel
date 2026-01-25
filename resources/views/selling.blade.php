<!DOCTYPE html>
<html>
<head>
    <title>Selling - Supermarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; margin-bottom: 30px; }
        .container { text-align: center; max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .nav-buttons { display: flex; gap: 10px; margin-bottom: 30px; }
        .nav-btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn-manage { background: #2196F3; color: white; }
        .btn-store { background: #4CAF50; color: white; }
        .sell-form { background: #f9f9f9; padding: 30px; border-radius: 5px; margin-bottom: 20px; }
        select, input { padding: 12px; margin: 10px 0; width: 100%; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .btn-sell { background: #FF9800; color: white; padding: 15px; width: 100%; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin-top: 10px; }
        .btn-sell:hover { background: #F57C00; }
        .message { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .product-info { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Supermarket Management System</h1>
        <h2>Selling Page</h2>

        <div class="nav-buttons">
            <a href="{{ route('manage') }}" class="nav-btn btn-manage">Manage Inventory</a>
            <a href="{{ route('store') }}" class="nav-btn btn-store">View Store</a>
        </div>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif

        @if($products->count() > 0)
            <div class="sell-form">
                <h2>Make a Sale</h2>
                <form method="POST" action="{{ route('sell.process') }}">
                    @csrf
                    <label>Select Product:</label>
                    <select name="product_id" id="product_id" required>
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price }}" 
                                    data-stock="{{ $product->stock }}">
                                {{ $product->name }} (ID: {{ $product->id }}) - Stock: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>

                    <div id="product-details" class="product-info" style="display:none;">
                        <p><strong>Price:</strong> LKR <span id="price">0</span></p>
                        <p><strong>Available Stock:</strong> <span id="stock">0</span></p>
                    </div>

                    <label>Quantity:</label>
                    <input type="number" name="quantity" min="1" value="1" required>

                    <button type="submit" class="btn-sell">Complete Sale</button>
                </form>
            </div>
        @else
            <div class="message error">No products available. Please add products in Manage Inventory.</div>
        @endif
    </div>

    <script>
        const selectElement = document.getElementById('product_id');
        const detailsDiv = document.getElementById('product-details');
        
        selectElement.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (this.value) {
                document.getElementById('price').textContent = selectedOption.dataset.price;
                document.getElementById('stock').textContent = selectedOption.dataset.stock;
                detailsDiv.style.display = 'block';
            } else {
                detailsDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
