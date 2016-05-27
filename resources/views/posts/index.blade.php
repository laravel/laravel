@extends('main')
@section('title', '| All Posts')
@section('content')
	<div class="row">
		<div class="col-md-10">
			<h1>All Posts</h1>
		</div>
		<div class="col-md-2">
			<a href="{{ route('posts.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing">Create New Post</a>
		</div>
		<div class="col-md-12">
			<hr>	
		</div>
	</div> <!-- end of .row -->
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<th>#</th>
					<th>Title</th>
					<th>Body</th>
					<th>Created At</th>
					<th>Action</th>
				</thead>
				<tbody>
					@foreach ($posts as $post)
						<tr>
							<th>{{ $post->id }}</th>
							<th>{{ $post->title }}</th>
							<th>{{ substr($post->body, 0, 50) }}{{ strlen($post->body) > 50 ? "..." : "" }}</th>
							<th>{{ date('M j, Y', strtotime($post->created_at)) }}</th>
							<th>
								{!! Html::linkroute('posts.show', 'View', array($post->id), array('class' => 'btn btn-default btn-sm spacing-top btn-block')) !!}
								{!! Html::linkroute('posts.edit', 'Edit', array($post->id), array('class' => 'btn btn-primary btn-sm spacing-top btn-block')) !!}
							</th>
						</tr>
					@endforeach
				</tbody>
			</table>
			<div class="text-center">
				{!! $posts->links(); !!}	
			</div>			
		</div>
	</div>
@stop