<li class="nav-item dropdown">
    <a {{ $attributes->merge([
                                'class' => 'nav-link dropdown-toggle',
                                'href' => $link ?? '#',
                                'data-bs-toggle' => 'dropdown',
                                'role' => 'button',
                                'aria-expanded' => 'true'
                            ]) }}>
        @if($icon)<i class="nav-icon {{ $icon }} d-block d-lg-none d-xl-block"></i>@endif
        @if($title)<span>{{ $title }}</span>@endif
    </a>
    <div class="dropdown-menu {{ $open ? 'show' : '' }}" data-bs-popper="static">
    {!! $slot !!}
    </div>
</li>
