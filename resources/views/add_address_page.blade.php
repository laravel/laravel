@extends('nav_layout')

@section('title', 'Add Address')
@section('content')


<div class="container mx-auto py-8 bg-white">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-lg mx-auto">
        <h2 class="text-2xl font-semibold mb-4 text-center">Add New Address</h2>
        
        <form id="address_insert" method="POST" class="mx-auto">
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea  id="address" placeholder="Enter full address" name="address" class="mt-1 p-2 block w-full bg-slate-100 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"  cols="10" rows="4"></textarea>
            </div>

            <div class="mb-4">
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input type="text" id="city" name="city" class="mt-1 p-2 block w-full rounded-md bg-slate-100 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Enter your city" required>
            </div>

            <div class="mb-4">
                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                <input type="text" id="state" name="state" class="mt-1 p-2 block w-full rounded-md bg-slate-100 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Enter your state" required>
            </div>

            <div class="mb-4">
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input type="text" id="country" name="country" class="mt-1 p-2 block w-full rounded-md bg-slate-100 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Enter your country" required>
            </div>

            <div class="mb-4">
                <label for="pincode" class="block text-sm font-medium text-gray-700">Pincode</label> 
                <input type="text" id="pincode" name="pincode" class="mt-1 p-2 block w-full rounded-md bg-slate-100 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Enter your pincode code" required>
            </div>

            <div class="flex justify-end">
                <button type="button" id="address_insert_btn" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save Address</button>
            </div>
        </form>
    </div>
</div>









@section('scripts')

@include('scripts.add_address_page_script')


@endsection




@stop