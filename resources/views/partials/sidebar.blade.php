<div class="p-3">
    <!-- معلومات المستخدم المختصرة -->
    <div class="text-center mb-4">
        <div class="user-avatar mx-auto mb-2" style="width: 60px; height: 60px; font-size: 1.5rem;">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
        <small class="text-muted">
            @if(auth()->user()->isAgency())
                وكيل رئيسي
            @elseif(auth()->user()->isSubagent())
                سبوكيل
            @elseif(auth()->user()->isCustomer())
                عميل
            @endif
        </small>
    </div>
    
    <hr>
    
    <!-- قائمة حسب نوع المستخدم -->
    <ul class="nav flex-column">
        @if(auth()->user()->isAgency())
            <!-- قائمة الوكيل الأساسي -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.dashboard') ? 'active' : '' }}" href="{{ route('agency.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.subagents.*') ? 'active' : '' }}" href="{{ route('agency.subagents.index') }}">
                    <i class="fas fa-users me-2"></i> السبوكلاء
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.customers.*') ? 'active' : '' }}" href="{{ route('agency.customers.index') }}">
                    <i class="fas fa-user-friends me-2"></i> العملاء
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.services.*') ? 'active' : '' }}" href="{{ route('agency.services.index') }}">
                    <i class="fas fa-cogs me-2"></i> الخدمات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.requests.*') ? 'active' : '' }}" href="{{ route('agency.requests.index') }}">
                    <i class="fas fa-file-alt me-2"></i> الطلبات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.quotes.*') ? 'active' : '' }}" href="{{ route('agency.quotes.index') }}">
                    <i class="fas fa-tag me-2"></i> عروض الأسعار
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.transactions.*') ? 'active' : '' }}" href="{{ route('agency.transactions.index') }}">
                    <i class="fas fa-money-bill me-2"></i> المعاملات المالية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.reports.*') ? 'active' : '' }}" href="{{ route('agency.reports.index') }}">
                    <i class="fas fa-chart-bar me-2"></i> التقارير
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agency.settings.*') ? 'active' : '' }}" href="{{ route('agency.settings.index') }}">
                    <i class="fas fa-cog me-2"></i> الإعدادات
                </a>
            </li>
        @elseif(auth()->user()->isSubagent())
            <!-- قائمة السبوكيل -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subagent.dashboard') ? 'active' : '' }}" href="{{ route('subagent.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subagent.services.*') ? 'active' : '' }}" href="{{ route('subagent.services.index') }}">
                    <i class="fas fa-cogs me-2"></i> الخدمات المتاحة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subagent.requests.*') ? 'active' : '' }}" href="{{ route('subagent.requests.index') }}">
                    <i class="fas fa-file-alt me-2"></i> طلبات عروض الأسعار
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subagent.quotes.*') ? 'active' : '' }}" href="{{ route('subagent.quotes.index') }}">
                    <i class="fas fa-tag me-2"></i> عروض الأسعار المقدمة
                </a>
            </li>
        @elseif(auth()->user()->isCustomer())
            <!-- قائمة العميل -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.services.*') ? 'active' : '' }}" href="{{ route('customer.services.index') }}">
                    <i class="fas fa-cogs me-2"></i> الخدمات المتاحة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.requests.*') ? 'active' : '' }}" href="{{ route('customer.requests.index') }}">
                    <i class="fas fa-file-alt me-2"></i> طلباتي
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.quotes.*') ? 'active' : '' }}" href="{{ route('customer.quotes.index') }}">
                    <i class="fas fa-tag me-2"></i> عروض الأسعار
                </a>
            </li>
        @endif
    </ul>
    
    <hr>
    
    <!-- روابط الملف الشخصي والخروج -->
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                <i class="fas fa-user-edit me-2"></i> الملف الشخصي
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="{{ route('logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form-sidebar').submit();">
                <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
            </a>
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>
