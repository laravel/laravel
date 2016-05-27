@extends('main')
@section('title', '| Edit Blog Post')
@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection
@section('content')
	<div class="row">
	{!! Form::model($post, ['route' => ['posts.update', $post->id], 'data-parsley-validate' => '', 'method' => 'PUT']) !!}
		<div class="col-md-8">
			<div class="well">
				<h1>Edit Post</h1>
				<hr>
			    {{ Form::label('title', 'Title:') }}
			    {{ Form::text('title', null, ['class' => 'form-control', 'required' => '', 'maxlength' => '255']) }}

			    {{ Form::label('slug', 'Slug:', ['class' => 'form-spacing-top']) }}
			    {{ Form::text('slug', null, ['class' => 'form-control', 'required' => '', 'minlength' => '5', 'maxlength' => '255' ]) }}

			    {{ Form::label('body', 'Post Body:', ['class' => 'form-spacing-top']) }}
			    {{ Form::textarea('body', null, ['class' => 'form-control', 'required' => '']) }}
			</div>
		</div>
		<div class="col-md-4">
			<div class="well">
				<dl class="dl-horizontal">
					<dt>Created At:</dt>
					<dd>{{ date('M j, Y H:ia', strtotime($post->created_at)) }}</dd>
				</dl>
				<dl class="dl-horizontal">
					<dt>Last Updated:</dt>
					<dd>{{ date('M j, Y H:ia', strtotime($post->updated_at)) }}</dd>
				</dl>
				<hr>
				<div class="row">
					<div class="col-sm-6">
						{!! Html::linkroute('posts.show', 'Cancel', array($post->id), array('class' => 'btn btn-danger btn-block btn-sm spacing-top')) !!}
					</div>
					<div class="col-sm-6">
						{{ Form::submit('Save Changes', ['class' => 'btn btn-success btn-block btn-sm spacing-top']) }}
					</div>
				</div>
			</div>
		</div>
	{!! Form::close() !!}
	</div>
@endsection
@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
@endsection