<div class="position-sticky pt-5 mt-3 sidebar-sticky">
    @auth
        @if(auth()->user()->isAgency())
            <!-- قائمة الوكيل الأساسي -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>إدارة الوكالة</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.dashboard') ? 'active' : '' }}" href="{{ route('agency.dashboard') }}">
                        <i class="fa fa-dashboard me-2"></i> لوحة التحكم
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.subagents.*') ? 'active' : '' }}" href="{{ route('agency.subagents.index') }}">
                        <i class="fa fa-users me-2"></i> السبوكلاء
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.customers.*') ? 'active' : '' }}" href="{{ route('agency.customers.index') }}">
                        <i class="fa fa-user-tie me-2"></i> العملاء
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.services.*') ? 'active' : '' }}" href="{{ route('agency.services.index') }}">
                        <i class="fa fa-cogs me-2"></i> الخدمات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.requests.*') ? 'active' : '' }}" href="{{ route('agency.requests.index') }}">
                        <i class="fa fa-file-alt me-2"></i> الطلبات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.quotes.*') ? 'active' : '' }}" href="{{ route('agency.quotes.index') }}">
                        <i class="fa fa-tags me-2"></i> عروض الأسعار
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.transactions.*') ? 'active' : '' }}" href="{{ route('agency.transactions.index') }}">
                        <i class="fa fa-money-bill me-2"></i> المعاملات المالية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.reports.*') ? 'active' : '' }}" href="{{ route('agency.reports.index') }}">
                        <i class="fa fa-chart-bar me-2"></i> التقارير
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('agency.settings.*') ? 'active' : '' }}" href="{{ route('agency.settings.index') }}">
                        <i class="fa fa-cog me-2"></i> الإعدادات
                    </a>
                </li>
            </ul>
        @elseif(auth()->user()->isSubagent())
            <!-- قائمة السبوكيل -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>خدمات السبوكيل</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('subagent.dashboard') ? 'active' : '' }}" href="{{ route('subagent.dashboard') }}">
                        <i class="fa fa-dashboard me-2"></i> لوحة التحكم
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('subagent.services.*') ? 'active' : '' }}" href="{{ route('subagent.services.index') }}">
                        <i class="fa fa-cogs me-2"></i> الخدمات المتاحة
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('subagent.requests.*') ? 'active' : '' }}" href="{{ route('subagent.requests.index') }}">
                        <i class="fa fa-file-alt me-2"></i> طلبات الأسعار
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('subagent.quotes.*') ? 'active' : '' }}" href="{{ route('subagent.quotes.index') }}">
                        <i class="fa fa-tags me-2"></i> عروضي
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('subagent.transactions.*') ? 'active' : '' }}" href="{{ route('subagent.transactions.index') }}">
                        <i class="fa fa-money-bill me-2"></i> حسابي
                    </a>
                </li>
            </ul>
        @elseif(auth()->user()->isCustomer())
            <!-- قائمة العميل -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>خدمات العميل</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                        <i class="fa fa-dashboard me-2"></i> لوحة التحكم
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.services.*') ? 'active' : '' }}" href="{{ route('customer.services.index') }}">
                        <i class="fa fa-cogs me-2"></i> الخدمات المتاحة
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.requests.*') ? 'active' : '' }}" href="{{ route('customer.requests.index') }}">
                        <i class="fa fa-file-alt me-2"></i> طلباتي
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.quotes.*') ? 'active' : '' }}" href="{{ route('customer.quotes.index') }}">
                        <i class="fa fa-tags me-2"></i> عروض الأسعار
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.transactions.*') ? 'active' : '' }}" href="{{ route('customer.transactions.index') }}">
                        <i class="fa fa-money-bill me-2"></i> المدفوعات
                    </a>
                </li>
            </ul>
        @endif
    @endauth
</div>
