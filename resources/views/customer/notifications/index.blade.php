@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الإشعارات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-bell me-2"></i> الإشعارات</h2>
        </div>
        <div class="col-md-6 text-end">
            @if($notifications->count() > 0)
            <button id="mark-all-read" class="btn btn-outline-primary">
                <i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="text-center p-5">
                    <img src="{{ asset('img/no-notifications.svg') }}" alt="لا توجد إشعارات" width="120" class="mb-3">
                    <h5>لا توجد إشعارات</h5>
                    <p class="text-muted">ستظهر هنا إشعارات النظام والتحديثات على طلباتك.</p>
                </div>
            @else
                <div class="list-group list-group-flush notification-list">
                    @foreach($notifications as $notification)
                        <div class="list-group-item notification-item {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex">
                                <div class="notification-icon me-3">
                                    @if(isset($notification->data['quote_id']))
                                        <i class="fas fa-file-invoice-dollar p-2 bg-primary text-white rounded-circle"></i>
                                    @elseif(isset($notification->data['request_id']))
                                        <i class="fas fa-clipboard-list p-2 bg-success text-white rounded-circle"></i>
                                    @else
                                        <i class="fas fa-bell p-2 bg-info text-white rounded-circle"></i>
                                    @endif
                                </div>
                                <div class="notification-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">
                                            @if(isset($notification->data['quote_id']))
                                                تحديث على عرض السعر #{{ $notification->data['quote_id'] }}
                                            @elseif(isset($notification->data['request_id']))
                                                تحديث على الطلب #{{ $notification->data['request_id'] }}
                                            @else
                                                إشعار جديد
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-2">
                                        @switch($notification->type)
                                            @case('App\Notifications\QuoteStatusChanged')
                                                @if(isset($notification->data['status']))
                                                    @switch($notification->data['status'])
                                                        @case('pending')
                                                            تم تقديم عرض سعر جديد وهو قيد المراجعة.
                                                            @break
                                                        @case('agency_approved')
                                                            تمت الموافقة على عرض السعر من قبل الوكالة وهو جاهز للمراجعة.
                                                            @break
                                                        @case('customer_approved')
                                                            لقد قمت بقبول عرض السعر وسيبدأ العمل على طلبك قريباً.
                                                            @break
                                                        @case('agency_rejected')
                                                            تم رفض عرض السعر من قبل الوكالة.
                                                            @break
                                                        @case('customer_rejected')
                                                            لقد قمت برفض عرض السعر.
                                                            @break
                                                        @default
                                                            تم تحديث حالة عرض السعر.
                                                    @endswitch
                                                @else
                                                    تم تحديث حالة عرض السعر.
                                                @endif
                                                @break
                                            @default
                                                {{ $notification->data['message'] ?? 'إشعار جديد من النظام' }}
                                        @endswitch
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if(isset($notification->data['quote_id']))
                                                <a href="{{ route('customer.quotes.show', $notification->data['quote_id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> عرض التفاصيل
                                                </a>
                                            @elseif(isset($notification->data['request_id']))
                                                <a href="{{ route('customer.requests.show', $notification->data['request_id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> عرض التفاصيل
                                                </a>
                                            @endif
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-link mark-read-btn" data-id="{{ $notification->id }}" {{ $notification->read_at ? 'disabled' : '' }}>
                                                <i class="fas fa-check me-1"></i> تحديد كمقروء
                                            </button>
                                            <form action="{{ route('customer.notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                                    <i class="fas fa-trash me-1"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center p-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تحديد إشعار واحد كمقروء
        const markReadButtons = document.querySelectorAll('.mark-read-btn');
        markReadButtons.forEach(button => {
            button.addEventListener('click', function() {
                markAsRead([this.dataset.id]);
                this.disabled = true;
                this.closest('.notification-item').classList.remove('bg-light');
            });
        });
        
        // تحديد كل الإشعارات كمقروءة
        const markAllReadBtn = document.getElementById('mark-all-read');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                const allIds = Array.from(document.querySelectorAll('.mark-read-btn:not([disabled])'))
                                   .map(btn => btn.dataset.id);
                if (allIds.length > 0) {
                    markAsRead(allIds);
                    // تحديث واجهة المستخدم
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('bg-light');
                    });
                    document.querySelectorAll('.mark-read-btn').forEach(btn => {
                        btn.disabled = true;
                    });
                }
            });
        }
        
        // وظيفة لتحديد الإشعارات كمقروءة
        function markAsRead(ids) {
            fetch('{{ route("customer.notifications.mark-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث عداد الإشعارات في القائمة الرئيسية (إذا كان موجوداً)
                    const notificationCounter = document.querySelector('.notification-counter');
                    if (notificationCounter) {
                        const currentCount = parseInt(notificationCounter.textContent);
                        const newCount = Math.max(0, currentCount - ids.length);
                        notificationCounter.textContent = newCount;
                        if (newCount === 0) {
                            notificationCounter.classList.add('d-none');
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>
@endpush

<style>
    .notification-item {
        transition: background-color 0.3s;
    }
    
    .notification-icon {
        min-width: 40px;
    }
</style>
@endsection
