@extends('layouts.adIndex')
@section('title')
	发送消息角色管理
@stop
@section('crumbs')
	发送消息角色管理
@stop
@section('search')
{{ Form::open(array('url' => '/admin/addRole','enctype'=>"multipart/form-data",'method'=>'post')) }}
    {{Form::label('role_name','角色昵称:');}}{{Form::text('role_name','',array('class'=>'form-control' ,'style'=>'width:200px;display:inline'))}}
    @if($errors->first('role_name'))
    	<span class="label label-danger">{{$errors->first('role_name')}}</span>
    @endif
    <br/>
    {{Form::label('sportrait','角色头像:')}}{{Form::file('sportrait',array('style'=>'width:200px;display:inline'));}}
    @if($errors->first('sportrait'))
    	<span class="label label-danger">{{$errors->first('sportrait')}}</span>
    @endif
    <br/>
    {{Form::submit('添加角色',array('class'=>"btn btn-default"));}}
{{ Form::close() }}

@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>角色id</th>
			<th>角色昵称</th>
			<th>角色头像</th>
			<th>修改</th>
		</tr>
		@foreach($rs as $item)
			<tr>
				<td>{{$item['id']}}</td>
				<td>{{$item['role_name']}}</td>
				<td>{{ HTML::image("$item[sportrait]", '角色头像', array( 'width' => 150, 'height' => 150 )) }}</td>
				<td><a href="/admin/modifyRole/{{$item['id']}}" target='_blank'>修改</a></td>
			</tr>
		@endforeach
	</table>
@stop

