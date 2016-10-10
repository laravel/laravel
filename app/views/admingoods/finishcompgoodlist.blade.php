@extends('layouts.adIndex')
@section('title')
	添加结束比赛商品列表
@stop
@section('crumbs')
	添加结束比赛商品列表
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/addFinishCompGoods",'method'=>'post'));}}
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>商品名称</th>
			<th>描述信息</th>
			<th>价格</th>
			<th>商品时效</th>
			<th>比赛id</th>
			<th>光盘价格</th>
			<th>更新</th>
		</tr>
		@foreach($list as $k=>$v)
			<tr>
				<td>{{$v['id']}}</td>
				<td>{{$v['name']}}</td>
				<td>{{$v['description']}}</td>
				<td>{{$v['price']}}</td>
				<td>次</td>
				<td>{{$v['competition_id']}}</td>
				<td>{{$v['cd_price']}}</td>
				<td><a href="/admin/updateFinishCompGoods/{{$v['id']}}">更新</a></td>
			</tr>
		@endforeach
	</table>
{{ Form::close() }}
@stop

