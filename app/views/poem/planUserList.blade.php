@extends('layouts.adIndex')
@section('title')
	计划任务用户列表
@stop
@section('search')
	<form action="/admin/planUserList" method='get'>
		<table>
			<tr>
				<td>用户uid</td>
				<td style="width:100px"><input id = 'uid' name="uid" type='text' value="{{$uid}}" class="form-control" /></td>
                <td>用户昵称</td>
				<td style="width:300px"><input id = 'nick' name="nick" type='text' value="{{$nick}}" class="form-control" /></td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
                
                
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	计划任务用户列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th></th>
            <th>uid</th>
			<th>昵称</th>
			<th>操作</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td><input type="checkbox" name="ck_id" class="ck_id" value="{{$item['id']}}" />{{$item['id']}}</td>
            <td>{{$item['uid']}}</td>
			<td>{{$item['nick']}}</td>
			<td>
				<button class="deletebt btn btn-mini btn-danger" type="button"  value='{{$item['id']}}'>删除</button>
			</td>
		</tr>
		@endforeach
	</table>
	{{ $list->links()  }}
    
    <div>
    <table border="0">
        <tr>
        <td>UID:</td>
        <td><input id='add_uid' name="add_uid" type='text' value="" class="form-control" style="width:100px;" /></td>
        <td><input class="add btn btn-mini btn-success" type="submit"  value='添加' /></td>
        </tr>
    </table>
    </div>
<script language="javascript">
$(".deletebt").click(function(){
	var id=$(this).val();
	$.get('/admin/planUserDel/',{id:id},function(data){
		window.alert('删除成功！');
		window.location.reload();
	});
});
$(".add").click(function(){
	var uid=$("#add_uid").val();
	$.get('/admin/planUserAdd/',{uid:uid},function(data){
		window.alert('添加成功！');
		window.location.reload();
	});
});
</script>
@stop

