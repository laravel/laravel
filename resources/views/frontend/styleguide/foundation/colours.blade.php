<div class="flex flex-wrap">
	@foreach ($colours as $colour)
		<div class="w-1/2 sm:w-1/6 md:w-1/12">
			<placeholder class="pt-full bg-{{ $colour }}">
				<div>
					<span style="background-color: rgba(0, 0, 0, 0.5);" class="inline-block text-white text-xs align-top">
						{{ $colour }}
					</span>
				</div>
			</placeholder>
		</div>
	@endforeach
</div>
