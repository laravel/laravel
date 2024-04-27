@extends('frontlayout')
@section('content')
<div class="container my-4">


	<h3 class="mb-3">Service Detail</h3>
	
  @foreach($service as $data) 	
	 <h3 class="mb-3">{{$data->title}}</h3>
	<p>{{$data->detail_desc}}</p>  
	<p> Hi</p>
</div>
  @endforeach
