@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">Reset Password</div>
			<div class="panel-body">

				@include('partials.errors.basic')

				<form class="form-horizontal" role="form" method="POST" action="/password/reset">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="token" value="{{ $token }}">
					<div class="form-group">
						<label for="email" class="col-sm-3 control-label">Email</label>
						<div class="col-sm-6">
							<input type="email" id="email" name="email" class="form-control" placeholder="Email" autocapitalize="off" value="{{ old('email') }}">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-3 control-label">Password</label>
						<div class="col-sm-6">
							<input type="password" name="password" class="form-control" placeholder="Password">
						</div>
					</div>
					<div class="form-group">
						<label for="password_confirmation" class="col-sm-3 control-label">Confirm Password</label>
						<div class="col-sm-6">
							<input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-3">
							<button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-refresh"></i>Reset Password</button>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
@stop
