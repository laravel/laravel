@extends('nav_layout')

@section('title', 'User Info')

@section('content')


<div class="mx-auto mb-14">
  <div class="relative flex flex-col mt-6 mx-auto bg-white shadow-lg rounded-xl w-96">
    <div class="p-6 text-white bg-blue-500 rounded-t-xl">
      <h5 class="text-xl font-semibold text-center text-white">User Details</h5>
    </div>

    <div class="p-6 border-t border-gray-200">
      <p class="text-lg font-semibold">Name:</p>
      <p class="mt-2 text-gray-700">{{ $user_data->full_name }}</p>
    </div>

    <div class="p-6 border-t border-gray-200">
      <p class="text-lg font-semibold">Email:</p>
      <p class="mt-2 text-gray-700">{{ $user_data->email }}</p>
    </div>

    <div class="p-6 border-t border-gray-200">
      <p class="text-lg font-semibold">Phone:</p>
      <p class="mt-2 text-gray-700">{{ $user_data->phone }}</p>
    </div>

    <div class="p-6 border-t border-gray-200">
      <a href="/change-password" class="mt-1 block w-full py-2 px-4 text-xl text-center bg-blue-500 border text-white rounded-xl hover:bg-blue-600 ">
        Change Password
      </a>
      
    </div>

    <div class="p-6 border-t border-gray-200">
      <a href="/get-data-address" class="mt-1 block w-full py-2 px-4 text-xl text-center bg-blue-500 border text-white rounded-xl hover:bg-blue-600 ">
         Address
      </a>
    </div>
  </div>
</div>







@section('scripts')
<!-- @include('scripts.place_order_page_script') -->
@endsection

@stop