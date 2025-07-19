@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="{{ asset('templates/NiceAdmin/assets/img/logo.png') }}" alt="">
            @else
                {!! $slot !!}
            @endif
        </a>
    </td>
</tr>