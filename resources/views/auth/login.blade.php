@extends('layouts.app')

@section('content')
<div class="container">
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">Login</div>
			<div class="panel-body">

				@include('partials.errors.basic')

				<form class="form-horizontal" role="form" method="POST" action="/auth/login">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
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
						<div class="col-sm-offset-3 col-sm-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="remember"> Remember Me
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-3">
							<button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-sign-in"></i>Login</button>
						</div>
						<div class="col-sm-3">
							<div id="forgot-password-link" class="text-right"><a href="/password/email">Forgot Your Password?</a></div>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
</div>
@stop
