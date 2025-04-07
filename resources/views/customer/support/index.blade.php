@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الدعم الفني</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-headset me-2"></i> الدعم الفني</h2>
            <p class="text-muted">يمكنك التواصل معنا في أي وقت للحصول على المساعدة</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">إرسال استفسار</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.support.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">الموضوع</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">الرسالة</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" required></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="request_id" class="form-label">متعلق بطلب (اختياري)</label>
                            <select class="form-select" id="request_id" name="request_id">
                                <option value="">-- غير متعلق بطلب معين --</option>
                                @foreach(auth()->user()->requests()->latest()->get() as $request)
                                    <option value="{{ $request->id }}">#{{ $request->id }} - {{ $request->service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">إرسال الاستفسار</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات الاتصال</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fas fa-phone me-2 text-primary"></i> الهاتف</h6>
                        <p class="text-muted">+966 12 345 6789</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-envelope me-2 text-primary"></i> البريد الإلكتروني</h6>
                        <p class="text-muted">support@example.com</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-clock me-2 text-primary"></i> ساعات العمل</h6>
                        <p class="text-muted">من الأحد إلى الخميس<br>8:00 صباحاً - 4:00 مساءً</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">الأسئلة الشائعة</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                    كيف يمكنني طلب خدمة جديدة؟
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    يمكنك طلب خدمة جديدة من خلال الانتقال إلى صفحة الخدمات واختيار الخدمة المطلوبة ثم النقر على زر "طلب الخدمة".
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    كيف يمكنني تتبع حالة طلبي؟
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    يمكنك تتبع حالة طلبك من خلال صفحة "طلبات الخدمة" في لوحة التحكم، والنقر على رقم الطلب لعرض التفاصيل.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    كيف يمكنني إلغاء طلب؟
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    يمكنك إلغاء الطلب إذا كان في حالة "قيد الانتظار" فقط. انتقل إلى تفاصيل الطلب واضغط على زر "إلغاء الطلب".
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
