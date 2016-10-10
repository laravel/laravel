@extends('layouts.adIndex')
@section('title')
	今日头条监测列表
@stop
@section('crumbs')
	今日头条监测列表 <span style="color:red">总数量:{{$count}}</span><span style="margin-left:20px">有效用户:</span><span style="color:red">{{$effect_user}}</span>
@stop
@section('search')
	@include('advmonitor.search')
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>广告计划</th>
			<th>渠道(点击/查看)</th>
			<th>平台</th>
			<th>访问时间</th>
			<th>ip地址</th>
			<th>用户行为</th>
		</tr>
		@foreach($list as $v)
			<tr>
				<td>{{$v['id']}}</td>
				<td>{{$v['adid']}}</td>
				<td>
					@if($v['cid'] == 1)
						查看
					@elseif($v['cid'] == 2)
						点击
					@endif
				</td>
				<td>
					@if($v['os'] == 0)
						安卓
					@elseif($v['os'] == 1)
						IOS
					@endif
				</td>
				<td>{{date('Y-m-d H:i:s',$v['timestamp'])}}</td>
				<td>{{$v['ip']}}</td>
				<td>
					@if($v['status'] == 0)
						没动作
					@elseif($v['status'] == 1)
						注册
					@elseif($v['status'] == 2)
						登陆
					@elseif($v['status'] == 3)
						第三方登陆
					@endif
				</td>
			</tr>
		@endforeach
	</table>
	{{  $list->appends($search)->links(); }}
@stop

