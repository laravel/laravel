@extends('frontlayout')
@section('content')
<div class="container my-4">
	<h3 class="mb-3">Add Testimonial</h3>
	@if(Session::has('success'))
	<p class="text-success">{{session('success')}}</p>
	@endif
	<form method="post" action="{{url('customer/save-testimonial')}}">
		@csrf
		<table class="table table-bordered">
			<tr>
				<th>Testimonial<span class="text-danger">*</span></th>
				<td><textarea name="testi_cont" class="form-control" rows="8"></textarea></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" class="btn btn-primary" /></td>
			</tr>
		</table>
	</form>
</div>
@endsection