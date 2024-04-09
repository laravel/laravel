@extends('admin.admin_layout')

@section('title', 'Product View')

@section('content')




<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/admin-products-datatable">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">View</li>
 </ol>
</nav>



<div class="container mt-5">
    <div class="row bg-white mt-5">


        <div class="d-flex justify-content-center">
            <div class="col-md-6 text-center">
                <img src="{{ asset('/storage/product_images/' . $product->image) }}"  style="height: 300px;width:300px;"   class="img-fluid" alt="Product Image">
            </div>
            <div class="col-md-6 " style="margin-top: 350px;margin-left: -450px;">
                <h2>{{ $product->name }}</h2>
            </div>
        </div>



        <div class="p-4  rounded row justify-content-center">




            <div class="col-md-3">
                <p><strong>Category:</strong> {{ $product->category_name }}</p>
                <p><strong>Description:</strong> {{ $product->description }}</p>
                <p><strong>Price:</strong> {{ $product->price }}</p>
            </div>

            <div class="col-md-3">

                <p><strong>Discount Amount:</strong> {{ $product->discount_amount }}</p>
                <p><strong>Discount Percent:</strong> {{ $product->discount_percent }}</p>
                <p><strong>Stock Quantity:</strong> {{ $product->stock_quantity }}</p>
            </div>

            <div class="col-md-3">
                <p><strong>Weight:</strong> {{ $product->weight }}</p>
                <p><strong>Color:</strong> <span>{{ $product->color }}</span></p>
                <p><strong>Company:</strong> {{ $product->company_name }}</p>
            </div>

            <div class="col-md-3">
                <p><strong>Created At:</strong> {{ $product->created_at }}</p>
                <p><strong>Updated At:</strong> {{ $product->updated_at }}</p>
            </div>

        </div>





    </div>
</div>

@endsection