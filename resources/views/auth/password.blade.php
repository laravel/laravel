@extends('app')

@section('content')
<div class="container">
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">Forgotten Password</div>
			<div class="panel-body">

				@include('partials.errors.basic')

				@if (Session::has('status'))
					<div class="alert alert-success">
						{{ Session::get('status') }}
					</div>
				@endif

				<form class="form-horizontal" role="form" method="POST" action="/password/email">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<label for="email" class="col-sm-3 control-label">Email</label>
						<div class="col-sm-6">
							<input type="email" id="email" name="email" class="form-control" placeholder="Email" autocapitalize="off" value="{{ old('email') }}">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-3">
							<button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-envelope"></i>Send Password Reset Link</button>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
</div>
@stop
