@extends('layouts/app')

@section('content')

	<e-input id="name" label="First name"></e-input>
	<e-input id="search" label="First name" type="search"></e-input>
	<e-input id="name-2" label="First name sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?" type="checkbox"></e-input>
	<e-input id="name-3" label="First name" type="radio"></e-input>

	<e-label for="test" text="test select"></e-label>

	<e-select id="select-test" label="First name" :options="[{'name': 'test', 'value': 1}]"></e-select>

	<button class="button">test</button>

	<div class="grid">
		<div class="grid__item tablet:w-8/24 tablet:mt-0">
			<div style="background: blue;color: white;">
				grid item
			</div>
		</div>

		<div class="grid__item tablet:w-8/24 tablet:mt-0">
			<div style="background: blue;color: white;">
				grid item
			</div>
		</div>

		<div class="grid__item tablet:w-8/24 tablet:mt-0">
			<div style="background: blue;color: white;">
				grid item
			</div>
		</div>
	</div>

@endsection
