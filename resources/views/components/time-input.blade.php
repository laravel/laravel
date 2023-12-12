@props(['disabled' => false])

<div class="input-group">
    <div class="input-group-addon">
        <i class="fa fa-clock-o"></i>
    </div>
    <x-text-input :disabled="$disabled" :attributes="$attributes"></x-text-input>
</div>
