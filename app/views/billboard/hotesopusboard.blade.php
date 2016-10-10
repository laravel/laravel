@extends('layouts.adIndex')
@section('title')
	最火作品榜
@stop
@section('crumbs')
	最火作品榜
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>用户id</th>
			<th>用户昵称</th>
            <th>性别</th>
			<th>作品id<i class="icon-search"></i></th>
			<th>作品名称</th>
			<th>作品赞数</th>
			<th>作品收听数</th>
			<th>作品转发数</th>
			<th>作品添加时间</th>
			<th>修改</th>
			<th>删除/恢复</th>
		</tr>
		@foreach ($hostesopuslist1 as $item)
		<tr>
			<td>{{$item['uid']}}</td>
			<td>{{$item['nick']}}</td>
            <td>
            @if($item['gender']==1)
                男
            @else
                女
            @endif
            </td>
			<td>{{$item['id']}}</td>
			<td>{{$item['name']}}</td>
			<td>{{$item['praisenum']}}</td>
			<td>{{$item['lnum']}}</td>
			<td>{{$item['repostnum']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				<button class="modify btn btn-mini btn-danger" type="button" value='{{$item['id']}}'>修改</button>
			</td>
			<td>
				@if($item['isdel'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|{{$item['uid']}}|0'>删除</button>
				@elseif($item['isdel'] == 1) 
					<button class="operator btn btn-mini btn-success" type="button" value='{{$item['id']}}|{{$item['uid']}}|1'>恢复</button>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $hostesopuslist->links()  }}
<script type="text/javascript">
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var uidSign = $(this).val();
				var arr = uidSign.split('|');
				var opusid = arr[0];
				var uid = arr[1];
				var sign = arr[2];
				$.post('/admin/delOrDelOpus',{opusid:opusid,uid:uid,sign:sign},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop

