@extends('layouts.adIndex')
@section('title')
	精品推荐
@stop
@section('crumbs')
	精品推荐
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id<i class="icon-search"></i></th>
			<th>主标题</th>
			<th>副标题</th>
			<th>下载地址</th>
			<th>图标</th>
			<th>排序</th>
			<th>所属平台</th>
			<th>注册时间</th>
			<th>禁用/启用</th>
		</tr>
		@foreach ($recommend as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['title']}}</td>
			<td>{{$item['subhead']}}</td>
			<td>{{$item['url']}}</td>
			<td><img style="width:50px;height:50px" src='{{$item['sicon']}}' /></td>
			<td>{{$item['sort']}}</td>
			<td>
				@if($item['platform'] == 1)
					android
				@elseif($item['platform'] == 0)
					苹果
				@endif
			</td>
			<td>{{date('Y/m/d H:i',$item['addtime'])}}</td>
			<td>
				@if($item['isdel'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}'>删除</button>
				@endif	
			</td>
		</tr>
		@endforeach
	</table>
<script type="text/javascript">
	//禁用/启用
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var id = $(this).val();
				$.post('/admin/delOrDelRecommenda',{id:id},function(data) {
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

