@extends('layouts')

@section('title', 'Product-Add')

@section('content')

<div class="container mx-auto  mt-5">
    <h2 class="mb-4 text-2xl font-semibold">Add New Product</h2>
    <form method="POST" id="product_add" enctype="multipart/form-data">
        <!-- Product Name -->
        <div class="mb-4">
            <label for="productName" class="block text-sm font-medium text-gray-700 required ">Product Name</label>
            <input type="text" class="form-input mt-1 block w-full rounded-md bg-slate-100 h-7 pl-1 " id="productName" name="productName" required>
        </div>


        
        <div class="mb-4">
        <label for="productImage" class="block text-sm font-medium text-gray-700 required"> Select Category:</label>

            <select class="form-select mt-1 block w-full rounded-md" id="category" name="category">
              
            </select>
        </div>



        <!-- Company -->
        <div class="mb-4">
        <label for="company" class="block text-sm font-medium text-gray-700 required"> Select Company:</label>

            <select class="form-select mt-1 block w-full rounded-md" id="company" name="company">
              
            </select>
        </div>



        <!-- Product Image -->
        <div class="mb-4">
            <label for="productImage" class="block text-sm font-medium text-gray-700 required">Product Image</label>
            <input type="file" class="form-input mt-1 block w-full rounded-md bg-slate-100  h-7 pl-1 " id="image" name="image[]" accept="image/jpeg, image/png, image/jpg" required>
        </div>
        <!-- Color -->
        <div class="mb-4">
            <label for="color" class="block text-sm font-medium text-gray-700 required">Color</label>
            <input type="text" class="form-input mt-1 block w-full rounded-md bg-slate-100 h-7 pl-1 " id="color" name="color">
        </div>
        <!-- Weight -->
        <div class="mb-4">
            <label for="weight" class="block text-sm font-medium text-gray-700 required">Weight</label>
            <input type="text" class="form-input mt-1 block w-full rounded-md bg-slate-100 h-7 pl-1 " id="weight" name="weight">
        </div>
        <!-- Quantity -->
        <div class="mb-4">
            <label for="quantity" class="block text-sm font-medium text-gray-700 required">Quantity</label>
            <input type="number" class="form-input mt-1 block w-full rounded-md bg-slate-100 h-7 pl-1 " id="quantity" name="quantity" required>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700 required">Price</label>
            <input type="number" class="form-input mt-1 block w-full rounded-md bg-slate-100 h-7 pl-1 " id="price" name="price" step="0.01" required>
        </div>

        <div class="mb-4">
            <label for="discount" class="block text-sm font-medium text-gray-700 required">Discount</label>
            <input type="text"  placeholder="Enter discount (e.g., Rs10 or 10%)" class="form-input mt-1  block w-full rounded-md h-7 pl-1  bg-slate-100" id="discount" name="discount" required>
        </div>
        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 required">Description</label>
            <textarea class="form-textarea mt-1 block w-full rounded-md bg-slate-100 h-7 pl-1 " id="description" name="description" rows="3"></textarea>
        </div>
        <!-- Price -->
  
        <!-- Submit Button -->
        <button type="button" id="add_btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Product</button>
    </form>
</div>

@section('scripts')
    @include('scripts.product_add_script')
@endsection

@stop
