@extends('frontlayout')
@section('content')
<div class="container my-4">
	<h3 class="mb-3">Contact Us</h3>

	@if($errors->any())
	    @foreach($errors->all() as $error)
	        <p class="text-danger">{{$error}}</p>
	    @endforeach
	@endif

	@if(Session::has('success'))
	<p class="text-success">{{session('success')}}</p>
	@endif
	
	<form method="post" action="{{url('save-contactus')}}">
		@csrf
		<table class="table table-bordered">
			<tr>
				<th>Full Name<span class="text-danger">*</span></th>
				<td><input type="text" name="full_name" class="form-control" /></td>
			</tr>
			<tr>
				<th>Email<span class="text-danger">*</span></th>
				<td><input type="email" name="email" class="form-control" /></td>
			</tr>
			<tr>
				<th>Subject<span class="text-danger">*</span></th>
				<td><input type="text" name="subject" class="form-control" /></td>
			</tr>
			<tr>
				<th>Message<span class="text-danger">*</span></th>
				<td><textarea name="msg" class="form-control" rows="8"></textarea></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" class="btn btn-primary" /></td>
			</tr>
		</table>
	</form>
</div>
@endsection