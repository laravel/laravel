@extends('layouts.adIndex')
@section('title')
	范读导师
@stop
@section('search')
	范读导师
@stop
@section('crumbs')
@stop
@section('content')
<style type="text/css">
.sex{ background-color:transparent; border:0px; width:50px; }
.sex2{ background-color:#FFF; border:1px solid #CCC; width:50px; }
</style>
	<table class="table table-hover table-bordered ">
		<tr>
			<th>导师id</th>
			<th>导师姓名</th>
		</tr>
		@foreach($list as $k=>$v)
		<tr>
			<td>{{$v['id']}}</td>
			<td>{{$v['name']}}</td>
		</tr>
		@endforeach
	</table>
@stop

