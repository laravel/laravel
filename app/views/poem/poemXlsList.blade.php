@extends('layouts.adIndex')
@section('title')
	伴奏xls添加计划列表
@stop
@section('search')
<a href="/admin/poemXlsExec" target="_blank">执行计划任务</a>
@stop
@section('crumbs')
	伴奏xls添加计划列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>计划时间</th>
			<th>文件路径</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		@foreach ($poemxlslist as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td><input class="plan_time" data-id="{{$item['id']}}" value='{{date("Y-m-d H:i",$item["plan_time"])}}'></td>
			<td>{{$item['name']}}</td>
			<td>
            @if($item['status'] == 2)
                <span style="color:green;">已执行</span>
            @elseif($item['status'] == 1)
                <span style="color:red;">已删除</span>
            @else
                未执行
            @endif
            </td>
			<td>
				<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}'>删除</button>
				@if($item['status'] == 0)
					<button class="doimport btn btn-mini btn-danger" type="button" data-value='{{$item['name']}}' value='{{$item['id']}}'>执行</button>
				@else
					<span style="color:green;">已执行</span>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $poemxlslist->links()  }}
<script language="javascript">
$(".operator").click(function(){
	var id=$(this).val();
	$.get('/admin/delOrExecXls',{id:id},function(data){
		window.alert(data);
		window.location.reload();
	});
});
//执行导入操作
$('.doimport').click(function(){
	var id = $(this).val();
	$.get('/admin/importPoem',{id:id},function(data){
		if(data == 1){
			window.alert('执行成功');
		}
		else{
			alert(data);
		}
		window.location.reload();
	});
});
/**
 * 更新计划任务执行时间
 *	@author:wang.hongli
 * @since:2016/05/15
 */
$('.plan_time').bind('focusout',function(){
	var id = $(this).attr('data-id');
	var plan_time = $(this).val();
	$.get('/admin/updatePoemPlanTime',{id:id,plan_time:plan_time},function(data){
		if(data == 'error'){
			alert('失败，请重试');
			return;
		}
	});
});

</script>
@stop

