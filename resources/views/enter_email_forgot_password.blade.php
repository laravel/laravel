@extends('layouts')




@section('title', 'Forgot Password')


@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<div style=" box-shadow: 10px 0px 80px 30px rgba(0, 0, 0, 0.1); width: 512px; " class=" mt-24  mx-auto  w-fit flex items-center justify-center rounded-md  bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="justify-items-center flex justify-center items-center ">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900" style="width: 340px;">
                Forgot Password
            </h2>
        </div>

        <form id="emailForm" class="mt-8 space-y-6" method="POST">
            <!-- <input type="hidden" name="remember" value="true"> -->
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" id="email" name="email" class="form-input rounded-lg bg-slate-100 w-full h-10 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required placeholder="Enter your email">
                </div>
            </div>
            <div>
                <button type="button" id="forgot_password_btn" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">

                    <div>
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <!-- Heroicon name: solid/lock-closed -->
                            <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5 8V5a5 5 0 0110 0v3h2a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1v-8a1 1 0 011-1h2zm3-3a3 3 0 016 0v3H8V5zm5 10v2H7v-2h6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                    <div id="span_btn_forgot">
                        Send Password Reset Link
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>




@section('scripts')


@include('scripts.enter_email_forgot_password_script')


@endsection



@stop