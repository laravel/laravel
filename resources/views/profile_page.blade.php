@extends('nav_layout')

@section('title', 'User Profile')

@section('content')


<div class="mx-auto mb-14">
  <div class="relative flex flex-col mt-6 mx-auto bg-white shadow-lg bg-clip-border rounded-xl w-96">
    <div class="p-6  rounded-t-xl">
      <h5 class="text-xl font-semibold text-center text-blue-600">
        {{ $name }}
      </h5>
    </div>

    <div class="p-6  rounded-xl" style="margin-top: -32px !important;"    >
      <a href="/orders-page" class="mt-1 block w-full py-2 px-4 text-xl text-center bg-blue-500 border text-white rounded-xl hover:bg-blue-600 ">
       My Orders
      </a>
    </div>

    <div  class="p-6   rounded-xl"  style="margin-top: -32px !important;"      >
      <a href="/user-info-profile" class="mt-1 block w-full py-2 px-4 text-xl text-center bg-blue-500 border text-white rounded-xl hover:bg-blue-600 ">
       User Info
      </a>
    </div>
   
  </div>
</div>






@section('scripts')
<!-- @include('scripts.place_order_page_script') -->
@endsection

@stop