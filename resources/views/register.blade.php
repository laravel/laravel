@extends('layouts')

@section('title', 'Register')

@section('content')
<div class="container mx-auto mt-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
        <div class="px-8 py-6 shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Register</h2>
            <!-- Registration Form -->
            <form id="registration" class="px-2 register_page_form" method="post">
                @csrf
                <!-- First Name and Last Name Inputs (Horizontal Layout) -->
                <div class="flex flex-wrap -mx-2 mb-4">
                    <div class="w-1/2 px-2">
                        <label for="first_name" class="block text-gray-700 text-lg mb-2">First Name</label>
                        <input type="text" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="first_name" name="first_name" required>
                    </div>
                    <div class="w-1/2 px-2">
                        <label for="last_name" class="block text-gray-700 text-lg mb-2">Last Name</label>
                        <input type="text" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="last_name" name="last_name" required>
                    </div>
                </div>
                <!-- Pincode Input -->
                <div class="mb-4">
                    <label for="pincode" class="block text-gray-700 text-lg mb-2">Pincode</label>
                    <input type="text" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="pincode" name="pincode" minlength="6" maxlength="6" required>
                </div>
                <!-- Email Input -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-lg mb-2">Email</label>
                    <input type="email" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="email" name="email" required>
                </div>
                <!-- Phone Input -->
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-lg mb-2">Phone</label>
                    <input type="number" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="phone" name="phone" maxlength="10" pattern="[0-9]*" inputmode="numeric" required>
                </div>
                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-lg mb-2">Password</label>
                    <input type="password" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="password" name="password" required>
                </div>
                <!-- Confirm Password Input -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 text-lg mb-2">Confirm Password</label>
                    <input type="password" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="mb-4">
                    <label for="City" class="block text-gray-700 text-lg mb-2">City</label>
                    <input type="text" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="city" name="city"  required>
                </div>

                <div class="mb-4">
                    <label for="state" class="block text-gray-700 text-lg mb-2">State</label>
                    <input type="text" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="state" name="state"  required>
                </div>

                <div class="mb-4">
                    <label for="country" class="block text-gray-700 text-lg mb-2">Country</label>
                    <input type="text" class="form-input w-full h-12 rounded-md bg-slate-100 border-gray-300" id="country" name="country"  required>
                </div>

                <!-- Address Input -->
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-lg mb-2">Address</label>
                    <textarea class="form-textarea w-full rounded-md bg-slate-100" id="address" name="address" rows="3" required></textarea>
                </div>
                <!-- Submit Button -->
                <button type="button" id="reg_submit" class="bg-blue-500 hover:bg-blue-600 text-white text-lg py-2 px-4 rounded w-full">Register</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('scripts.register_script')
@endsection