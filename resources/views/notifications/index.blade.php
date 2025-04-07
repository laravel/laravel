@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الإشعارات</li>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-bell me-2"></i> الإشعارات</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <button id="markAllRead" class="btn btn-primary">
                <i class="fas fa-check-double me-1"></i> تعليم الكل كمقروء
            </button>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد إشعارات حتى الآن.
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <a href="{{ $notification->action_url }}" class="list-group-item list-group-item-action notification-item {{ $notification->is_read ? 'read' : 'unread' }}" data-notification-id="{{ $notification->id }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    @if(!$notification->is_read)
                                        <span class="badge rounded-pill bg-{{ $notification->type === 'danger' ? 'danger' : ($notification->type === 'warning' ? 'warning' : ($notification->type === 'success' ? 'success' : 'primary')) }} me-2"></span>
                                    @endif
                                    {{ $notification->title }}
                                </h5>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->message }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $notification->created_at->format('Y-m-d H:i') }}</small>
                                <button class="btn btn-sm btn-outline-secondary mark-as-read" data-notification-id="{{ $notification->id }}" {{ $notification->is_read ? 'disabled' : '' }}>
                                    <i class="fas fa-check"></i> تعليم كمقروء
                                </button>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تعليم إشعار كمقروء
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const notificationId = this.dataset.notificationId;
                markAsRead(notificationId, this);
            });
        });
        
        // تعليم جميع الإشعارات كمقروءة
        document.getElementById('markAllRead').addEventListener('click', function() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        
                        const markButton = item.querySelector('.mark-as-read');
                        if (markButton) {
                            markButton.setAttribute('disabled', 'disabled');
                        }
                        
                        const badge = item.querySelector('.badge');
                        if (badge) {
                            badge.remove();
                        }
                    });
                    
                    // تحديث عدد الإشعارات في الهيدر
                    updateNotificationBadge(0);
                }
            });
        });
        
        // وظيفة لتعليم إشعار كمقروء
        function markAsRead(id, button) {
            fetch(`/notifications/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationItem = button.closest('.notification-item');
                    notificationItem.classList.remove('unread');
                    notificationItem.classList.add('read');
                    button.setAttribute('disabled', 'disabled');
                    
                    const badge = notificationItem.querySelector('.badge');
                    if (badge) {
                        badge.remove();
                    }
                    
                    // تحديث عدد الإشعارات في الهيدر
                    updateNotificationCount();
                }
            });
        }
        
        // تحديث عدد الإشعارات غير المقروءة
        function updateNotificationCount() {
            fetch('{{ route("notifications.unread") }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.count);
                });
        }
        
        // تحديث علامة عدد الإشعارات في الهيدر
        function updateNotificationBadge(count) {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    });
</script>
@endpush

<style>
    .notification-item.unread {
        background-color: #f8f9fa;
        border-right: 3px solid #007bff;
    }
    
    .notification-item.read {
        background-color: white;
    }
    
    .badge.rounded-pill {
        width: 10px;
        height: 10px;
        padding: 0;
        display: inline-block;
    }
</style>
@endsection
