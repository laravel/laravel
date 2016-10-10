@extends('layouts.adIndex')
@section('title')
	推广图片列表
@stop
@section('crumbs')
	推广图片列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>图片ID</th>
			<th>图片名称</th>
			<th>图片描述</th>
			<th>添加时间</th>
			<th>更新</th>			
		</tr>
		@foreach ($headphoto as $item)
			<tr>
				<td>{{$item['id']}}</td>
				<td>{{$item['name']}}</td>
				<td>{{$item['description']}}</td>
				<td>{{date("Y-m-d H:i:s",$item['addtime'])}}</td>
				<td><a class="btn btn-mini btn-success" href="/admin/updHeadPhotoView/{{$item['id']}}" target="_blank" >更新</a></td>
			</tr>
			@if ($item['icon'])
			<tr>
				<td colspan="13">
					<img src="{{$item['icon']}}">
				</td>
			</tr>
			@endif
		@endforeach
	</table>
{{-- {{ $headphoto->links()  }} --}}
@stop