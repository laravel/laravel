@extends('layouts/base')

@section('app')

	<div class="e-container py-16 space-y-16">
		<section class="space-y-5">
			<h1 class="e-h3">Fonts</h1>

			<div class="space-y-5">
				@foreach ($model['fonts'] as $font)
					<div class="space-y-2">
						<p>{{ $font }}</p>

						<p class="{{ $font }}">The quick brown fox jumps over the lazy dog</p>
					</div>
				@endforeach
			</div>
		</section>

		<section class="space-y-5">
			<h1 class="e-h3">Headings</h1>

			<div class="space-y-5">
				@foreach ($model['typography'] as $item)
					<div class="space-y-2">
						<p>{{ $item['name'] }}</p>

						<div class="{{ $item['class'] }}">{!! $item['copy'] !!}</div>
					</div>
				@endforeach
			</div>
		</section>

		<section class="space-y-5">
			<h1 class="e-h3">Colours</h1>

			<div class="grid grid-cols-2 gap-2 md:grid-cols-6 xl:grid-cols-8">
				@foreach ($model['colours'] as $colour)
					<div>
						<div class="e-placeholder pt-full bg-{{ $colour }} border border-grey-300"></div>

						<div class="p-2 text-center truncate border-l border-r border-b border-grey-300">
							{{ $colour }}
						</div>
					</div>
				@endforeach
			</div>
		</section>

		<section class="space-y-5">
			<h1 class="e-h3">Buttons</h1>

			<div class="e-copy space-y-5">
				@foreach ($model['buttons'] as $group)
					<div class="p-5 flex gap-x-5 {{ $group['bg'] }}">
						@foreach ($group['items'] as $button)
							<e-button v-bind='@json($button)'></e-button>
						@endforeach
					</div>
				@endforeach
			</div>
		</section>

		<section class="space-y-5">
			<h1 class="e-h3">Icons</h1>

			<div class="flex flex-wrap">
				@foreach ($model['icons'] as $icon)
					<div class="e-h1 flex-shrink-0 mr-2 mb-2" title="{{ $icon }}">
						<icon name="{{ $icon }}"></icon>
					</div>
				@endforeach
			</div>
		</section>
	</div>

@endsection
