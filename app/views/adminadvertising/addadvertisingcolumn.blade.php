@extends('layouts.adIndex')
@section('title')
	添加广告栏目
@stop
@section('crumbs')
	添加广告栏目
@stop
@section('search')
	{{Form::open(array('url'=>"/admin/addAdvertisingColumn",'method'=>'post',"enctype"=>"multipart/form-data"));}}
		{{Form::label('name','栏目名称',['style'=>'float:left'])}}
		<div class="col-xs-4">
			{{Form::text('name','',['class'=>'form-control'])}}
  		</div>
  		{{Form::submit('添加',['class'=>'btn btn-success'])}}
	{{Form::close()}}
@stop
@section('content')
	<table class="table table-hover table-bordered ">
	<tr>
		<td>id</td>
		<td>栏目名称</td>
		<td>添加时间</td>
	</tr>
	@foreach($data['list'] as $k=>$v)
		<tr>
			<td>{{$v['id']}}</td>
			<td>{{$v['name']}}</td>
			<td>{{date('Y-m-d',$v['addtime'])}}</td>
		</tr>
	@endforeach
	</table>
@stop

