@extends('layouts')

@section('title', 'Reset Password')

@section('content')



<div style="width: 512px;box-shadow: 10px 0px 80px 30px rgba(0, 0, 0, 0.1);" class="flex items-center bg-white justify-center  rounded-lg mt-24  mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset Your Password
            </h2>
        </div>
        <form class="mt-8 space-y-6" id="reset_password_form" method="POST">
            <input type="hidden" id="email_token" name="email_token" value="{{ $token_value }}">

            <div class="rounded-md shadow-sm ">
                <div>
                    <label for="password" class="sr-only">New Password</label>
                    <input id="password" name="password" type="password" required class="form-input rounded-lg bg-slate-100 w-full h-10 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="New Password">
                </div>
                <div class="mt-5">
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="form-input rounded-lg bg-slate-100 w-full h-10 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Confirm Password">
                </div>
            </div>

            <div>
                <button type="button" id="reset_password_btn" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>





@endsection

@section('scripts')
@include('scripts.reset_password_script')
@endsection