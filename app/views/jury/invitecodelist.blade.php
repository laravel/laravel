@extends('layouts.adIndex')
@section('title')
	邀请码列表
@stop
@section('crumbs')
	邀请码列表
@stop

@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>ID</th>
			<th>姓名</th>
			<th>邀请码</th>
			<th>电话</th>
			<th>地址</th>
			<th>添加时间</th>
			<th>操作</th>

		</tr>
		@if(!empty($list))
			@foreach ($list as $item)
			<tr id="tr_<?php echo $item["id"];?>">
				<td>{{$item['id']}}</td>
				<td>{{$item['name']}}</td>
				<td>{{$item['code']}}</td>
				<td>{{$item['mobile']}}</td>
                <td>{{$item['address']}}</td>
                <td><?php echo date("Y-m-d",$item['addtime']);?></td>
                <td>
                <a href="javascript:;" data-id="<?php echo $item["id"];?>" class="delete">删除</a>
                </td>
			</tr>
			@endforeach
		@endif
	</table>
    @if(!empty($list))
        {{ $list->links()  }}
    @endif
<script language="javascript">
$(".delete").click(function(){
	var id=$(this).attr("data-id");
	$.get("/admin/delInviteCode",{id:id},function(data){
		if(data==1){
			$("#tr_"+id).remove();
			window.alert("操作成功");
		}else{
			window.alert("error");
		}
	});
});

</script>
@stop


