@extends('nav_layout')

@section('title', 'Change Password')

@section('content')


<div class="mx-auto mt-8">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-500">
            <h5 class="text-xl font-semibold text-center text-white">Change Password</h5>
        </div>

        <div class="px-6 py-4">
            <form id="change_password_user" method="POST">
                <div class="mb-4">
                    <label for="current_password" class="block   mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-input change_password_page_input mt-1 block w-full bg-blue-100 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block   mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input mt-1 block w-full bg-blue-100 change_password_page_input rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block   mb-2">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input mt-1 block w-full bg-blue-100 change_password_page_input rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex justify-center">
                    <button type="button" id="btn_Change_Password" class="bg-blue-500 hover:bg-blue-600 text-white  py-2 px-4 rounded">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<br><br>





@section('scripts')
@include('scripts.change_password_script')
@endsection

@stop