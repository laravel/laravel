@extends('layouts/base')

@section('head')
	<style>
		.sg-preview {
			display: flex;
		}

		.sg-preview--horizontal {
		}

		.sg-preview--vertical {
			flex-direction: column;
		}

		.sg-preview__item {
		}

		.sg-preview--vertical .sg-preview__item {
			margin-top: 20px;
		}

		.sg-preview--vertical .sg-preview__item:first-child {
			margin-top: 0;
		}

		.sg-preview--horizontal .sg-preview__item {
			margin-left: 20px;
		}

		.sg-preview--horizontal .sg-preview__item:first-child {
			margin-left: 0;
		}
	</style>

	@include('layouts/partials/meta', [
		'stylesheet' => '/compiled/css/app.css',
	])

	<style>
		@if ($model['bg'] ?? false)
			body {
				background-color: {{ $model['bg'] }};
			}
		@endif
	</style>
@endsection

@section('app')
	<div class="sg-preview sg-preview--{{ $model['stack'] ?? true ? 'vertical' : 'horizontal' }}" style="{{ $model['style'] ?? '' }}">
		@foreach ($model['attributes'] as $attributes)
			<div class="sg-preview__item">
				@if ($model['container'] ?? false)
					<div class="e-container">
				@endif
					@if ($model['component'] ?? false)
						@if ($model['component']['type'] === 'vue')
							<component
								is="{{ $model['component']['name'] }}"
								v-bind='@json($attributes)'
							></component>
						@else
							@component('components/' . $model['component']['name'], $attributes)
							@endcomponent
						@endif
					@else
						@include('templates/styleguide/' . $model['partial'], $attributes)
					@endif
				@if ($model['container'] ?? false)
					</div>
				@endif
			</div>
		@endforeach
	</div>
@endsection

@section('app:after')
	<script src="/static/js/styleguide/iframeResizer.contentWindow.min.js"></script>

	@parent
@endsection
