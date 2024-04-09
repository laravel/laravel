@extends('layouts')




@section('title', 'Login')


@section('content')




<div class="container mx-auto">
    <div class="flex justify-center mt-5">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white shadow-2xl mx-auto rounded-lg p-8 mt-8" style="width: 600px; " >
                <h2 class="text-3xl font-semibold text-center mb-6">Login</h2>
                <!-- Login Form -->
                <form id="login_form" method="POST">
                    @csrf
                    <!-- Email or Phone Input -->
                    <div class="mb-4">
                        <label for="email_or_phone" class="block text-gray-700 font-semibold mb-2">Email address or Phone number</label>
                        <input type="text" id="email_or_phone" name="email_or_phone" class="form-input rounded-lg bg-slate-100 w-full h-10 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <!-- Password Input -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                        <input type="password" id="password" name="password" class="form-input rounded-lg w-full py-2 bg-slate-100 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <!-- Submit Button -->
                    <button type="button" id="btn_login" class="bg-blue-500 hover:bg-blue-600 text-lg text-white  py-2 px-6 rounded-lg w-full focus:outline-none focus:shadow-outline">Login</button>
                </form>
                <!-- Additional Links -->

                <div class="text-center mt-6">
                    <p class="hover:text-blue-600 text-gray-700  text-lg"><a href="/forgot-password" class="forget-password-link  text-gray-700 hover:text-blue-500  ">Forget Password? </a></p>
                </div>


                <div class="text-center mt-6">
                    <p class="text-gray-600">Don't have an account? <a href="/register" style="text-decoration: none;" class="text-blue-500 hover:text-blue-600 font-semibold hover:underline">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>






@section('scripts')


@include('scripts.login_script')


@endsection



@stop