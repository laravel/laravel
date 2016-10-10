@extends('layouts.adIndex')
@section('title')
	活动列表
@stop
@section('crumbs')
	活动列表
@stop

@section('search')
	<form action="/admin/addActivities" method='post'>
		<table>
			<tr>
				
                <td style="width:150px">
					活动名称
                </td>
                 <td style="width:150px">
                    <input id="name_x" type="text" name="name" value="" />
				</td>
				<td colspan=2>
					<input class="subbt btn btn-mini btn-success" type="button"  value='添加活动' />&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>
<script language="javascript">
$(".subbt").click(function(){
	var name=$("#name_x").val();
	$.post("/admin/addActivities",{name:name},function(data){
		if(data==1){
			window.alert("操作成功");
			window.location.reload();
		}else{
			window.alert("名称不能为空");
		}
	});
});
</script>
@stop

@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>活动名称</th>
            <th>添加时间</th>
		</tr>
		@if(!empty($list))
        @foreach ($list as $item)
        <tr>
            <td>{{$item['id']}}</td>
            <td>{{$item['name']}}</td>
            <td>{{date("Y-m-d H:i",$item['addtime'])}}</td>
        </tr>
        @endforeach
		@endif
	</table>
		@if(!empty($list))
			{{ $list->links()  }}
		@endif

@stop


