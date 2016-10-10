@extends('layouts.adIndex')
@section('title')
	作品举报
@stop

@section('crumbs')
	作品举报
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>作品id</th>
			<th>作品名称</th>
			<th>举报人id</th>
			<th>举报人</th>
			<th>被举报人</th>
			<th>举报原因<i class="icon-search"></i></th>
			<th>举报时间</th>
			<th>处理状态</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['opusid']}}</td>
			<td>{{$item['name']}}</td>
			<td>{{$item['fromid']}}</td>
			<td>{{$item['from_nick']}}</td>
			<td>{{$item['nick']}}</td>
			<td>{{$item['reason']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				@if($item['status'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['status']}}'>未处理</button>
					<input type="hidden" value='{{$item['id']}}' />
				@elseif($item['status'] == 1) 
					<button class="operator btn btn-mini btn-success" type="button" value='{{$item['status']}}'>已处理</button>
					<input type="hidden" value='{{$item['id']}}' />
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{$pages->links()}}
<script type="text/javascript">
	$(function() {
		$('.operator').each(function() {
			$(this).click(function() {
				var status = $(this).val();
				var id = $(this).next().val();
				$.post('/admin/modifyReportOpus',{status:status,id:id},function(data) {
					if('error' == data) {
						alert('修改举报状态失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop

