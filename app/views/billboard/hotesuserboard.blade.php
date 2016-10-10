@extends('layouts.adIndex')
@section('title')
	用户列表
@stop
@section('crumbs')
	用户列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id<i class="icon-search"></i></th>
			<th>昵称</th>
			<th>邮箱</th>
			<th>手机号</th>
			<th>性别</th>
			<th>等级</th>
			<th>赞数</th>
			<th>收听数</th>
			<th>转发数</th>
			<th>注册时间</th>
			<th>来源</th>
			<th>修改</th>
			<th>禁用/启用</th>
		</tr>
		@foreach ($hotesuserboard as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['nick']}}</td>
			<td>{{$item['email']}}</td>
			<td>{{$item['phone']}}</td>
			<td>
				@if($item['gender'] == 1)
					男
				@elseif($item['gender'] == 0)
					女
				@endif
			</td>
			<td>{{$item['grade']}}</td>
			<td>{{$item['praisenum']}}</td>
			<td>{{$item['lnum']}}</td>
			<td>{{$item['repostnum']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				@if($item['thpartType'] == 0) 
					<button class="operator btn btn-mini btn-danger" type="button" >本系统</button>
				@elseif($item['thpartType'] == 1) 
					<button class="operator btn btn-mini btn-danger" type="button" >新浪</button>
				@else
					<button class="operator btn btn-mini btn-danger" type="button" >QQ</button>
				@endif
			</td>
			<td>
				<button class="modify btn btn-mini btn-danger" type="button" value='{{$item['id']}}'>修改</button>
			</td>
			<td>
				@if($item['isdel'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|0'>禁用</button>
				@elseif($item['isdel'] == 1) 
					<button class="operator btn btn-mini btn-success" type="button" value='{{$item['id']}}|1'>启用</button>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $hotesuserboard->links()  }}
<script type="text/javascript">
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var uidSign = $(this).val();
				var arr = uidSign.split('|');
				var uid = arr[0];
				var sign = arr[1];
				$.post('/admin/delOrDelUser',{uid:uid,sign:sign},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	$(function() {
		$('.modify').each(function() {
			$(this).click(function() {
				var uid = $(this).val();
				alert(uid);
			});
		});
	});
</script>
@stop

