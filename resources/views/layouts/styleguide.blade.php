<!DOCTYPE html>
<html class="text-gray-800 antialiased" lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>{{ $model['meta']['title'] }}</title>
	<link rel="icon" type="image/png" href="{{ $model['meta']['icon'] }}">

	<link href="https://cdn.jsdelivr.net/npm/highlight.js@9.15.8/styles/github.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@1.0.4/dist/tailwind.min.css" rel="stylesheet">

	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700&display=swap" rel="stylesheet">

	<style>
		html {
			font-family: 'Source Sans Pro', sans-serif;
			scroll-behavior: smooth;
		}

		.icon {
			width: 1em;
			height: 1em;
			fill: none;
			stroke: currentColor;
			stroke-width: 2;
			stroke-linecap: round;
			stroke-linejoin: round;
		}

		.bg-accent {
			background-color: {{ $model['meta']['accent'] }};
		}

		.text-accent,
		.hover\:text-accent:hover {
			color: {{ $model['meta']['accent'] }};
		}
	</style>
</head>
<body>
	<div id="app">
		<header>
			<div class="container mx-auto py-16 px-4">
				<div class="flex items-center">
					<h1 class="text-5xl tracking-tighter text-gray-900 font-bold">Styleguide</h1>
				</div>
			</div>
		</header>

		<nav class="sticky left-0 top-0 w-full overflow-hidden bg-accent text-white z-20">
			<div class="container mx-auto px-4">
				<div class="flex items-center h-12">
					<ul class="flex -ml-4 font-semibold overflow-x-scroll">
						@foreach ($model['sections'] as $slug => $section)
							<li class="ml-4 flex-shrink-0">
								<a class="hover:underline" href="#{{ $slug }}">{{ $section['heading'] }}</a>
							</li>
						@endforeach
					</ul>

					<div class="ml-auto pl-4">
						<ul class="flex">
							<li>
								<button
									class="flex items-center h-8 focus:outline-none"
									:class="{ 'opacity-50': !$data.toggle.copy }"
									@click="$data.toggle.copy = !$data.toggle.copy"
								>
									<icon name="copy"></icon>

									<span class="ml-2 text-xs font-bold uppercase">Copy</span>
								</button>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</nav>

		<main class="mb-16">
			@foreach ($model['sections'] as $slug => $section)
				<section class="block -mt-12 pt-12" id="{{ $slug }}">
					<div class="container mx-auto px-4 mt-16">
						<h2 class="text-4xl text-gray-900 font-bold">
							<a href="#{{ $slug }}">{{ $section['heading'] }}</a>
						</h2>

						@if ($section['copy'] ?? false)
							<p v-if="$data.toggle.copy" class="max-w-3xl mt-4 text-xl">{{ $section['copy'] }}</p>
						@endif
					</div>

					@if ($section['blocks'])
						<div class="relative mt-2 pointer-events-none">
							<nav class="sticky left-0 top-0 w-full -mt-12 pt-12 z-10">
								<div class="bg-white pointer-events-auto">
									<div class="container mx-auto px-4">
										<div class="flex items-center h-12 border-b">
											<ul class="flex -ml-4 text-accent font-semibold overflow-x-scroll">
												@foreach ($section['blocks'] as $blockSlug => $block)
													<li class="ml-4 flex-shrink-0">
														<a class="hover:underline" href="#{{ $blockSlug }}">{{ $block['heading'] }}</a>
													</li>
												@endforeach
											</ul>
										</div>
									</div>
								</div>
							</nav>

							<div class="container mx-auto px-4">
								@foreach ($section['blocks'] as $blockSlug => $block)
									<section class="-mt-24 pt-24" id="{{ $blockSlug }}">
										<div class="mt-10 pointer-events-auto">
											<h3 class="text-2xl text-gray-900 font-semibold">
												<a href="#{{ $blockSlug }}">{{ $block['heading'] }}</a>
											</h3>

											@if ($block['copy'] ?? false)
												<p v-if="$data.toggle.copy" class="max-w-3xl mt-4 text-lg">{{ $block['copy'] }}</p>
											@endif

											@foreach ($block['previews'] as $previewSlug => $preview)
												<block
													class="block -mt-24 pt-24"
													block="{{ $blockSlug }}"
													id="{{ "$blockSlug-$previewSlug" }}"
													preview="{{ $previewSlug }}"
													section="{{ $slug }}"
													:attributes='@json($preview['attributes'])'
													:background-color='@json($preview['bg'] ?? null)'
													:component='@json($preview['component'] ?? null)'
													:autoload='@json($preview['autoload'] ?? true)'
												>
													@if ($preview['heading'] ?? false)
														<h4 class="mt-5 text-lg text-gray-900 font-semibold">
															<a href="{{ "#$blockSlug-$previewSlug" }}">{{ $preview['heading'] }}</a>
														</h4>

														@if ($preview['copy'] ?? false)
															<p v-if="$data.toggle.copy" class="max-w-3xl mt-4">{{ $preview['copy'] }}</p>
														@endif
													@endif

													<div v-if="false" class="mt-4 my-px" style="height: 200px;"></div>
												</block>
											@endforeach
										</div>
									</section>
								@endforeach
							</div>
						</div>
					@endif
				</section>
			@endforeach
		</main>
	</div>

	<script src="/static/js/styleguide/vue.min.js"></script>
	<script src="/static/js/styleguide/vue-observe-visibility.min.js"></script>
	<script src="/static/js/styleguide/iframeResizer.min.js"></script>
	<script src="/static/js/styleguide/highlight.min.js"></script>
	<script src="{{ mix('/compiled/js/styleguide.js') }}" async></script>
</body>
</html>
