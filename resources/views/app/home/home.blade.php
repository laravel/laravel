@extends('layouts/app')

@section('content')

	<h1 class="e-h1 m-test">test h1</h1>
	<h2 class="e-h2">test h2</h2>
	<h3 class="e-h3">test h3</h3>
	<h4 class="e-h4">test h4</h4>
	<h5 class="e-h5">test h5</h5>
	<h6 class="e-h6">test h6</h6>
	<a href="">anchor</a>

	<div class="e-copy">
		<h1>test h1</h1>
		<h2>test h2</h2>
		<h3>test h3</h3>
		<h4>test h4</h4>
		<h5>test h5</h5>
		<h6>test h6</h6>

		<ul>
			<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</li>
		</ul>

		<ol>
			<li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</li>
		</ol>
	</div>

	<placeholder class="pt-logo">
		<img src="/static/img/branding/logo.svg" alt="Engage logo">
	</placeholder>

	<placeholder>
		<img src="//placehold.it/1600x900" alt="">
	</placeholder>

	<placeholder class="pt-full">
		<img src="//placehold.it/1600" alt="">
	</placeholder>

	<e-input id="name" label="First name"></e-input>
	<e-input id="search" label="First name" type="search"></e-input>
	<e-input id="name-2" label="First name sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?" type="checkbox"></e-input>
	<e-input id="name-3" label="First name" type="radio"></e-input>

	<e-label for="test" text="test select"></e-label>

	<e-select id="select-test" label="First name" :options="[{'name': 'test', 'value': 1}]"></e-select>

	<e-button text="test"></e-button>

	<div class="e-grid">
		<div class="e-grid__item md:w-8/24 md:mt-0">
			<div style="background: blue;color: white;">
				e-grid item
			</div>
		</div>

		<div class="e-grid__item md:w-8/24 md:mt-0">
			<div style="background: blue;color: white;">
				e-grid item
			</div>
		</div>

		<div class="e-grid__item md:w-8/24 md:mt-0">
			<div style="background: blue;color: white;">
				e-grid item
			</div>
		</div>
	</div>

@endsection
