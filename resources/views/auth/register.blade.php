@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>تسجيل حساب جديد</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="user_type" class="form-label">نوع الحساب</label>
                            <select id="user_type" class="form-select @error('user_type') is-invalid @enderror" name="user_type" required>
                                <option value="">اختر نوع الحساب</option>
                                <option value="agency" {{ old('user_type') == 'agency' ? 'selected' : '' }}>وكيل أساسي</option>
                                <option value="subagent" {{ old('user_type') == 'subagent' ? 'selected' : '' }}>سبوكيل</option>
                                <option value="customer" {{ old('user_type') == 'customer' ? 'selected' : '' }}>عميل</option>
                            </select>
                            @error('user_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div id="agency_fields" class="d-none">
                            <div class="mb-3">
                                <label for="agency_name" class="form-label">اسم الوكالة</label>
                                <input id="agency_name" type="text" class="form-control @error('agency_name') is-invalid @enderror" name="agency_name" value="{{ old('agency_name') }}">
                                @error('agency_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div id="subagent_customer_fields" class="d-none">
                            <div class="mb-3">
                                <label for="agency_id" class="form-label">الوكالة التابع لها</label>
                                <select id="agency_id" class="form-select @error('agency_id') is-invalid @enderror" name="agency_id">
                                    <option value="">اختر الوكالة</option>
                                    @foreach(\App\Models\Agency::all() as $agency)
                                        <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                                @error('agency_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">تأكيد كلمة المرور</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                تسجيل
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">لديك حساب بالفعل؟ <a href="{{ route('login') }}">سجل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userTypeSelect = document.getElementById('user_type');
        const agencyFields = document.getElementById('agency_fields');
        const subagentCustomerFields = document.getElementById('subagent_customer_fields');
        
        function toggleFields() {
            if (userTypeSelect.value === 'agency') {
                agencyFields.classList.remove('d-none');
                subagentCustomerFields.classList.add('d-none');
            } else if (userTypeSelect.value === 'subagent' || userTypeSelect.value === 'customer') {
                agencyFields.classList.add('d-none');
                subagentCustomerFields.classList.remove('d-none');
            } else {
                agencyFields.classList.add('d-none');
                subagentCustomerFields.classList.add('d-none');
            }
        }
        
        userTypeSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
