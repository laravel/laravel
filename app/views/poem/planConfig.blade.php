@extends('layouts.adIndex')
@section('title')
	计划任务配置
@stop
@section('search')

@stop
@section('crumbs')
	计划任务配置
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th></th>
            <th>名称</th>
            <th>内容</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td><input type="checkbox" name="ck_id" class="ck_id" value="{{$item['id']}}" />{{$item['id']}}</td>
            <td>{{$item['name']}}</td>
			<td>
            <?php
            $tmp=unserialize($item['contents']);
			foreach($tmp as $k=>$v){
				echo $k.":".$v."<br>";
			}
			?>
            </td>
            <td>
            @if($item['status'] == 0)
                <span style="color:red;">已关闭</span>
            @else
                <span style="color:green;">已开启</span>
            @endif
            </td>
			<td>
				<button class="updatebt btn btn-mini btn-danger" type="button"  value='{{$item['id']}}'>修改</button>
                @if($item['status'] == 2)
                	<button class="closebt btn btn-mini btn-danger" type="button" value='{{$item['id']}}'>关闭</button>
                @else
                    <button class="openbt btn btn-mini btn-success" type="button" value='{{$item['id']}}'>开启</button>
                @endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $list->links()  }}
<script language="javascript">
$(".updatebt").click(function(){
	var id=$(this).val();
	window.location.href="/admin/updatePlanConfig?id="+id;
})
$(".closebt").click(function(){
	var id=$(this).val();
	$.get('/admin/upPlanConfigStatus',{id:id,status:0},function(data){
		if(data==1){
			window.alert('操作成功');
			window.location.reload();
		}else{
			window.alert('操作失败');
		}
	});
});
$(".openbt").click(function(){
	var id=$(this).val();
	$.get('/admin/upPlanConfigStatus',{id:id,status:2},function(data){
		if(data==1){
			window.alert('操作成功');
			window.location.reload();
		}else{
			window.alert('操作失败');
		}
	});
});
</script>  
@stop

