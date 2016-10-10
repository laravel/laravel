@extends('layouts.adIndex')
@section('title')
	诗经奖板
@stop
@section('crumbs')
	诗经奖板
@stop
@section('search')
	<form action="#" method='get'>
		<table>
			<tr>
				<td>伴奏ID</td>
				<td style="width:300px"><input id = 'poem_id' name="poem_id" type='text' class="form-control" /></td>
				<td colspan=2>
					<button class="add btn btn-mini btn-success" type="button">添加</button>
				</td>
			</tr>
		</table>
	</form>
@stop


@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>ID</th>
			<th>伴奏ID</th>
			<th>伴奏名称</th>
			<th>下载数</th>
			<th>读名</th>
			<th>写名</th>
			<th>操作</th>
		</tr>
        @if(!empty($list))
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['poem_id']}}</td>
			<td>{{$item['name']}}</td>
            <td>{{$item['downnum']}}</td>
            <td>{{$item['readername']}}</td>
            <td>{{$item['writername']}}</td>
			<td><button class="btn btn-mini btn-danger del" type="button"  data-id="{{$item['id']}}" >删除</button></td>
		</tr>
		@endforeach
        @endif
	</table>

<script type="text/javascript">
//添加
$('.add').click(function() {
	var poem_id = $("#poem_id").val();
	$.get('/admin/addShi',{poem_id:poem_id},function(data) {
		if(data==1) {
			alert('操作成功');
			window.location.reload();
		} else {
			alert('操作失败，稍后再试');
		}
	});
});
//删除
$('.del').click(function() {
	var id = $(this).attr("data-id");
	$.get('/admin/delShi',{id:id},function(data) {
		if(data==1) {
			alert('操作成功');
			window.location.reload();
		} else {
			alert('操作失败，稍后再试');
		}
	});
});
</script>
@stop


