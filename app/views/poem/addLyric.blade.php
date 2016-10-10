@extends('layouts.adIndex')
@section('title')
	佳作投稿
@stop

@section('crumbs')
	佳作投稿
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>投稿id</th>
			<th>用户id</th>
			<th>用户名</th>
			<th>新词内容<i class="icon-search"></i></th>
			<th>添加时间</th>
			<th>投稿类型</th>
			<th>审核状态</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>
				<input type="text" name="name" class="name form-control" value='{{$item['nick']}}' />
				
			</td>
			<td>{{$item['lyric']}}</td>
			<td>{{date('Y-m-d',$item['addtime'])}}</td>
			<td>
				@if($item['type'] == 0)
					<button class="btn btn-mini btn-danger" type="button"  value=''>美文推荐</button>
				@elseif($item['type'] == 1) 
					<button class="btn btn-mini btn-success" type="button" value=''>佳作投稿</button>
				@endif
			</td>
			<td>
				@if($item['ischecked'] == 0)
					<button class="operator btn btn-mini btn-success" type="button"  value='{{$item['ischecked']}}'>通过审核</button>
					<input type="hidden" name="addlyricid" class="addlyricid" value='{{$item['id']}}' />
				@elseif($item['ischecked'] == 1) 
					<button class="operator btn btn-mini btn-danger" type="button" value='{{$item['ischecked']}}'>未审核</button>
					<input type="hidden" name="addlyricid" class="addlyricid" value='{{$item['id']}}' />
				@endif
			</td>
		</tr>
		@endforeach
	</table>
<script type="text/javascript">
	$(function() {
		$('.operator').each(function() {
			$(this).click(function() {
				var checkstatus = $(this).val();
				var addlyricid = $(this).next().val();
				$.post('/admin/modifyAddLyric',{checkstatus:checkstatus,addlyricid:addlyricid},function(data) {
					if('error' == data) {
						alert('修改伴奏名称失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop

