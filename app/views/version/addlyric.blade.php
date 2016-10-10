@extends('layouts.adIndex')
@section('title')
	{{$title}}
@stop
@section('crumbs')
	{{$title}}
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>用户id</th>
			<th>用户昵称<i class="icon-search"></i></th>
            <th>性别</th>
			<th>内容</th>
			<th width="100">添加时间</th>
			<th>处理/未处理</th>
			<th>删除</th>
		</tr>
		@foreach ($addlyric as $item)
		<tr>	
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>{{$item['nick']}}</td>
            <td>
            @if($item['gender']==1)
                男
            @else
                女
            @endif
            </td>
			<td>{{$item['lyric']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			
			<td>
				@if(empty($item['ischecked']))
					<button class="operator btn btn-mini btn-danger" type="button">已处理</button>
				@else
					<button class="operator btn btn-mini btn-success" type="button">未处理</button>
				@endif
			</td>
			<td>
				<button class="operator btn btn-mini btn-danger" type="button"  value=''>删除</button>
			</td>
		</tr>
		@endforeach
	</table>
	{{ $addlyric->links()  }}
<script type="text/javascript">
	// $(function() {
	// 	$('.operator').each(function(){
	// 		$(this).click(function() {
	// 			var uidSign = $(this).val();
	// 			var arr = uidSign.split('|');
	// 			var opusid = arr[0];
	// 			var uid = arr[1];
	// 			var sign = arr[2];
	// 			$.post('/admin/delOrDelOpus',{opusid:opusid,uid:uid,sign:sign},function(data) {
	// 				if('error' == data) {
	// 					alert('操作失败，请重试');
	// 				} else {
	// 					location.reload();
	// 				}
	// 			});
	// 		});
	// 	});
	// });
	$(function() {
		$('.name').each(function() {
			$(this).focusout(function() {
				var name = $(this).val();
				var poemid = $(this).next().val();
				$.post('/admin/modifyPoemName',{poemid:poemid,name:name,type:1},function(data) {
					if('error' == data) {
						alert('修改伴奏名称失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	$(function() {
		$('.aliasname').each(function() {
			$(this).focusout(function() {
				var name = $(this).val();
				var poemid = $(this).next().val();
				$.post('/admin/modifyPoemName',{poemid:poemid,name:name,type:2},function(data) {
					if('error' == data) {
						alert('修改伴奏名称失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	$(function() {
		$('.spelling').each(function() {
			$(this).focusout(function() {
				var name = $(this).val();
				var poemid = $(this).next().val();
				$.post('/admin/modifyPoemName',{poemid:poemid,name:name,type:3},function(data) {
					if('error' == data) {
						alert('修改伴奏名称失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	$(function() {
		$('.allchar').each(function() {
			$(this).focusout(function() {
				var name = $(this).val();
				var poemid = $(this).next().val();
				$.post('/admin/modifyPoemName',{poemid:poemid,name:name,type:4},function(data) {
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

