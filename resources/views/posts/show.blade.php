@extends('main')
@section('title', '| View Post')
@section('content')
	<div class="row">
		<div class="col-md-8">
			<div class="well">
				<h1>{{ $post->title }}</h1>
				<p>{{ $post->body }}</p>
			</div>
		</div>
		<div class="col-md-4 well">
			<dl class="dl">
				<dt>Url:</dt>
				<!-- <dd><a href="{{ url('blog/'. $post->slug) }}">{{ url('blog/'. $post->slug) }}</a></dd> -->
				<dd><a href="{{ route('blog.single', $post->slug) }}">{{ route('blog.single', $post->slug) }}</a></dd>
				<!-- {!! Html::linkroute('blog.single', 'View This Post', array($post->slug), array('class' => 'btn btn-default btn-block btn-sm spacing-top')) !!} -->
			</dl>
			<dl class="dl">
				<dt>Created At:</dt>
				<dd>{{ date('M j, Y H:ia', strtotime($post->created_at)) }}</dd>
			</dl>
			<dl class="dl">
				<dt>Last Updated:</dt>
				<dd>{{ date('M j, Y H:ia', strtotime($post->updated_at)) }}</dd>
			</dl>
			<hr>
			<div class="row">
				<div class="col-md-6">
					<div align="center">
						{!! Html::linkroute('posts.edit', 'Edit', array($post->id), array('class' => 'btn btn-primary btn-sm spacing-top btn-block')) !!}
					</div>
				</div>
				<div class="col-md-6">
					<div align="center">
						{!! Form::open(['route' => ['posts.destroy', $post->id], 'method' => 'DELETE']) !!}
						{!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm spacing-top btn-block']) !!}
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div align="center">
				{!! Html::linkroute('posts.index', 'All Post', array($post->id), array('class' => 'btn btn-default btn-block btn-sm spacing-top')) !!}
			</div>
		</div>
	</div>
@endsection