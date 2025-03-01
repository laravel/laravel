<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Create a Product </h1>
    <form action="{{ route('product.store') }}" method="POST">
        @csrf
        @method('POST')
        <div>
            <label for="name">Name</label>
            <input type="text" placeholder="Name" name="name" id="name">

        </div>
        <div>
            <label for="qty">qty</label>
            <input type="text" placeholder="qty" name="qty" id="qty">
        </div>
        <div>
            <label for="price">Price</label>
            <input type="text" placeholder="Price" name="price" id="price">
        </div>
        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" cols="30" rows="10"></textarea>

        </div>
        <div>
            <button type="submit">Save a new Product</button>
        </div>
    </form>
</body>
</html>
