@extends('layouts.adIndex')
@section('title')
	修改推广图片
@stop
@section('crumbs')
	修改推广图片
@stop
@section('search')
@stop
@section('content')
	<form action ='/admin/updHeadPhoto' method = 'post' enctype="multipart/form-data" >
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="200px">图片名称</td>
				<td><input class="form-control input-xxlarge" id="name" name = "name" type="text" value="{{ $headphoto['name'] }}"></td>
			</tr>
			<tr>
				<td width="200px">推广图片</td>
				<td><input class="form-control input-xxlarge" id="icon" name = "icon" type="file" value="">
				<img src="{{ $headphoto['icon'] }}" alt="">
				</td>
			</tr>
			<tr>
				<td>图片描述信息</td>
			    <td><textarea rows="5" cols="80" name="description">{{ $headphoto['description'] }}</textarea></td>
			</tr>
			<input type="hidden" name = "id" value="{{ $headphoto['id'] }}">
			<tr text-align="center">
				<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
			</tr>
		</table>
	</form>
@stop