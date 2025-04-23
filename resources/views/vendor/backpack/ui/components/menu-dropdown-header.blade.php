<h6 {{ $attributes->merge(['class' => 'dropdown-header']) }}>
    @if ($icon != null)<i class="nav-icon {{ $icon }}"></i>@endif
    @if ($title!=null) <span>{{ $title }}</span>@endif
</h6>
