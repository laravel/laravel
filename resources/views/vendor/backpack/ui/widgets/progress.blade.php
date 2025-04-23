@php
  // defaults; backwards compatibility with Backpack 4.0 widgets
  $widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-lg-3';
@endphp

@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_start'))
  <div class="{{ $widget['class'] ?? 'card text-white bg-primary' }}">
    <div class="card-body">
      @if (isset($widget['value']))
      <div class="text-value">{!! $widget['value'] !!}</div>
      @endif

      @if (isset($widget['description']))
      <div>{!! $widget['description'] !!}</div>
      @endif
      
      @if (isset($widget['progress']))
      <div class="progress progress-white progress-xs my-2">
        <div class="progress-bar" role="progressbar" style="width: {{ $widget['progress']  }}%" aria-valuenow="{{ $widget['progress']  }}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      @endif
      
      @if (isset($widget['hint']))
      <small class="text-muted">{!! $widget['hint'] !!}</small>
      @endif
    </div>
    
    @if (isset($widget['footer_link']))
    <div class="card-footer px-3 py-2">
      <a class="btn-block text-muted d-flex justify-content-between align-items-center" href="{{ $widget['footer_link'] ?? '#' }}"><span class="small font-weight-bold">{{ $widget['footer_text'] ?? 'View more' }}</span><i class="la la-angle-right"></i></a>
    </div>
    @endif
  </div>
@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_end'))