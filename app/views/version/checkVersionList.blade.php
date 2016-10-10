@extends('layouts.adIndex')
@section('title')
	用户列表
@stop
@section('search')
	<form action="/admin/checkVersionList" method='get'>
		<table>
			<tr>
				<td>所属平台</td>
				<td style="width:300px">
					<input  type="radio" name="platform"  value="0" checked>IOS
					<input  type="radio" name="platform"  value="1" >android
				</td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />
				</td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	用户列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id<i class="icon-search"></i></th>
			<th>url</th>
			<th>所属平台</th>
			<th>描述信息</th>
			<th>版本号</th>
			<th>android比较版本号</th>
			<th>添加时间</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['url']}}</td>
			<td>
				@if($item['platform'] == 0)
					IOS
				@elseif($item['platform'] == 1)
					android
				@endif
			</td>
			<td>
				<textarea>{{$item['des']}}</textarea>
			</td>
			<td>{{$item['version']}}</td>
			<td>{{$item['version_code']}}</td>
			<td>{{date('Y-m-d H:i',$item['uptime'])}}</td>		
		</tr>
		@endforeach
	</table>
@stop

