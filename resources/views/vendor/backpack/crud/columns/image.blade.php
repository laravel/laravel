{{-- image column type --}}
@php
  $column['value'] = $column['value'] ?? data_get($entry, $column['name']);

  if($column['value']) {
    $column['height'] = $column['height'] ?? "25px";
    $column['width'] = $column['width'] ?? "auto";
    $column['radius'] = $column['radius'] ?? "3px";
    $column['prefix'] = $column['prefix'] ?? '';
    $column['temporary'] = $column['temporary'] ?? false;
    $column['expiration'] = $column['expiration'] ?? 1;

    if($column['value'] instanceof \Closure) {
      $column['value'] = $column['value']($entry);
    }

    if (is_array($column['value'])) {
      $column['value'] = json_encode($column['value']);
    }

    if (preg_match('/^data\:image\//', $column['value'])) { // base64_image
      $href = $src = $column['value'];
    } elseif (isset($column['disk'])) { // image from a different disk (like s3 bucket)

      if (!empty($column['temporary'])) {
          $href = $src = Storage::disk($column['disk'])->temporaryUrl($column['prefix'].$column['value'], now()->addMinutes((int) $column['expiration']));
      } else {
          $href = $src = Storage::disk($column['disk'])->url($column['prefix'].$column['value']);
      }

    } else { // plain-old image, from a local disk
      $href = $src = asset($column['prefix'] . $column['value']);
    }

    $column['wrapper']['element'] = $column['wrapper']['element'] ?? 'a';
    $column['wrapper']['href'] = $column['wrapper']['href'] ?? $href;
    $column['wrapper']['target'] = $column['wrapper']['target'] ?? '_blank';
  }
@endphp

<span>
  @if(empty($column['value']))
    {{ $column['default'] ?? '-' }}
  @else
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        <img src="{{ $src }}" style="
        max-height: {{ $column['height'] }};
        width: {{ $column['width'] }};
        border-radius: {{ $column['radius'] }};"
        />
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
  @endif
</span>
