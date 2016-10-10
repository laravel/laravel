@extends('layouts.adIndex')
@section('title')
	版本更新
@stop
@section('crumbs')
	版本更新
@stop
@section('content')
	<form action ='/admin/doAddVersion' method = 'post'>
	<table class="table table-hover table-bordered ">
		<tr>
			<td width="200px">版本地址(url)</td>
			<td><input class="input-xxlarge form-control" id="url" name = "url" type="text" value=""></td>
		</tr>
		<tr>
			<td>所属平台</td>
			<td>
				<input  type="radio" name="platform"  value="0" checked>IOS
				<input  type="radio" name="platform"  value="1" >android
			</td>
		</tr>
		<tr>
			<td>强制更新</td>
			<td for="force_update">
				<input type="checkbox" name="force_update" value='1' checked>				
			</td>
		</tr>
		<tr>
			<td>描述信息</td>
			<td>
				<textarea style="height:200px" class="form-control" name="des" id = "des"></textarea>
			</td>
		</tr>
		<tr>
			<td>版本号</td>
			<td><input class="form-control input-xxlarge" id="version" name="version" type="text" value=""></td>
		</tr>
		<tr>
			<td>android版本号(数字)</td>
			<td>
			  	<input class="form-control input-xxlarge" id="version_code" name="version_code" type="text" value="">
			</td>
		</tr>
		<tr text-align="center">
			<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
		</tr>
	</table>
	</form>
@stop

