# Templating

## Contents

- [The Basics](#the-basics)
- [Sections](#sections)
- [Blade Template Engine](#blade-template-engine)
- [Blade Control Structures](#blade-control-structures)
- [Blade Layouts](#blade-layouts)

<a name="the-basics"></a>
## The Basics

Your application probably uses a common layout across most of its pages. Manually creating this layout within every controller action can be a pain. Specifying a controller layout will make your development much more enjoyable. Here's how to get started:

#### Specify a "layout" property on your controller:

	class Base_Controller extends Controller {

		public $layout = 'layouts.common';

	}

#### Access the layout from the controllers' action:

	public function action_profile()
	{
		$this->layout->nest('content', 'user.profile');
	}

> **Note:** When using layouts, actions do not need to return anything.

<a name="sections"></a>
## Sections

View sections provide a simple way to inject content into layouts from nested views. For example, perhaps you want to inject a nested view's needed JavaScript into the header of your layout. Let's dig in:

#### Creating a section within a view:

	<?php Section::start('scripts'); ?>
		<script src="jquery.js"></script>
	<?php Section::stop(); ?>

#### Rendering the contents of a section:

	<head>
		<?php echo Section::yield('scripts'); ?>
	</head>

#### Using Blade short-cuts to work with sections:

	@section('scripts')
		<script src="jquery.js"></script>
	@endsection

	<head>
		@yield('scripts')
	</head>

<a name="blade-template-engine"></a>
## Blade Template Engine

Blade makes writing your views pure bliss. To create a blade view, simply name your view file with a ".blade.php" extension. Blade allows you to use beautiful, unobtrusive syntax for writing PHP control structures and echoing data. Here's an example:

#### Echoing a variable using Blade:

	Hello, {{ $name }}.

#### Echoing function results using Blade:

	{{ Asset::styles() }}

#### Render a view:

You can use **@include** to render a view into another view. The rendered view will automatically inherit all of the data from the current view.

	<h1>Profile</hi>
	@include('user.profile')

Similarly, you can use **@render**, which behaves the same as **@include** except the rendered view will **not** inherit the data from the current view.

	@render('admin.list')

#### Blade comments:

	{{-- This is a comment --}}

	{{--
		This is a
		multi-line
		comment.
	--}}

> **Note:** Unlike HTML comments, Blade comments are not visible in the HTML source.

<a name='blade-control-structures'></a>
## Blade Control Structures

#### For Loop:

	@for ($i = 0; $i <= count($comments); $i++)
		The comment body is {{ $comments[$i] }}
	@endfor

#### Foreach Loop:

	@foreach ($comments as $comment)
		The comment body is {{ $comment->body }}.
	@endforeach

#### While Loop:

	@while ($something)
		I am still looping!
	@endwhile

#### If Statement:

	@if ( $message == true )
		I'm displaying the message!
	@endif

#### If Else Statement:

	@if (count($comments) > 0)
		I have comments!
	@else
		I have no comments!
	@endif

#### Else If Statement:

	@if ( $message == 'success' )
		It was a success!
	@elseif ( $message == 'error' )
		An error occurred.
	@else
		Did it work?
	@endif

#### For Else Statement:

	@forelse ($posts as $post)
		{{ $post->body }}
	@empty
		There are not posts in the array!
	@endforelse

#### Unless Statement:

	@unless(Auth::check())
		Login
	@endunless

	// Equivalent to...

	<?php if ( ! Auth::check()): ?>
		Login
	<?php endif; ?>

<a name="blade-layouts"></a>
## Blade Layouts

Not only does Blade provide clean, elegant syntax for common PHP control structures, it also gives you a beautiful method of using layouts for your views. For example, perhaps your application uses a "master" view to provide a common look and feel for your application. It may look something like this:

	<html>
		<ul class="navigation">
			@section('navigation')
				<li>Example Item 1</li>
				<li>Example Item 2</li>
			@endsection
		</ul>

		<div class="content">
			@yield('content')
		</div>
	</html>

Notice the "content" section being yielded. We need to fill this section with some text, so let's make another view that uses this layout:

	@layout('master')

	@section('content')
		Welcome to the profile page!
	@endsection

Great! Now, we can simply return the "profile" view from our route:

	return View::make('profile');

The profile view will automatically use the "master" template thanks to Blade's **@layout** expression.

> **Important:** The **@layout** call must always be on the very first line of the file, with no leading whitespaces or newline breaks.

#### Appending with @parent

Sometimes you may want to only append to a section of a layout rather than overwrite it. For example, consider the navigation list in our "master" layout. Let's assume we just want to append a new list item. Here's how to do it:

	@layout('master')

	@section('navigation')
		@parent
		<li>Nav Item 3</li>
	@endsection

	@section('content')
		Welcome to the profile page!
	@endsection

**@parent** will be replaced with the contents of the layout's *navigation* section, providing you with a beautiful and powerful method of performing layout extension and inheritance.
