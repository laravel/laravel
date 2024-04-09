@extends('nav_layout')

@section('title', 'Select Address')

@section('content')

<div class="container mx-auto py-8 bg-white">
    <div class="max-w-md w-full p-6 mx-auto bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Select Address</h2>
        <form id="address_change_order" method="POST">

            <select id="address_select" name="address_order_select" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500">
                <option value="" disabled selected>Select an address</option>
                @foreach($address as $address)
                <option value="{{ $address->id }}" > {{ $address->full_address }}</option>
                @endforeach
            </select>

            <br>
            <div class="flex mt-4">
                <button type="button" id="add_address_btn" class="bg-red-500 ml-2 text-white px-4 py-2 rounded-md hover:bg-red-600">Add Address</button>

                <button type="button" id="save_address_btn" class="bg-blue-500 ml-16 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save Address</button>
            </div>

        </form>


    </div>
</div>

@endsection

@section('scripts')
@include('scripts.change_address_page_script')

@endsection