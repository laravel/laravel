@extends('layouts.adIndex')
@section('title')
	修改角色信息
@stop
@section('crumbs')
	修改角色信息
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/modifyRole/$rs[id]",'enctype'=>"multipart/form-data",'method'=>'post'));}}
	<table class="table table-hover table-bordered ">
		<tr>
			<th>角色id</th>
			<td>{{$rs['id'];}}</td>
		</tr>
		<tr>
			<th>{{Form::label('role_name','角色昵称:')}}</th>
			<td>{{Form::text('role_name',"$rs[role_name]",array('style'=>'width:200px;display:inline'));}}</td>
		</tr>
		<tr>
			<th>上传头像</th>
			<td>
				
				{{Form::label('sportrait','角色头像:')}}{{Form::file('sportrait',array('style'=>'width:200px;display:inline'));}}
			</td>
		</tr>
		<tr>
			<td>原头像</td>
			<td>
				{{ HTML::image("$rs[sportrait]", '角色头像', array( 'width' => 150, 'height' => 150 )) }}
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('更新',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
@stop

