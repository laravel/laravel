@extends('layouts.adIndex')
@section('title')
	运营人员列表,钻石，鲜花运营人员列表
@stop
@section('search')
	<form action="/admin/addOperationUser" method='post'>
		<table>
			<tr>
				<td>
				用户id:<input id = 'uid' name="uid" type='text' class="form-control" value="" />
				<input class="search btn btn-mini btn-success" type="submit"  value='添加' />
				</tr>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	运营人员列表,钻石，鲜花运营人员列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>ID</th>
			<th>用户id</th>
			<th>用户昵称</th>
			<th>后台添加人员</th>
			<th>操作时间</th>
			<th>操作</th>
		</tr>
		@if(!empty($rs))
			@foreach($rs as $k=>$v)
				<tr>
					<td>{{$v['id']}}</td>
					<td>{{$v['uid']}}</td>
					<td>{{$v['nick']}}</td>
					<td>admin</td>
					<td>{{date('Y-m-d H:i:s',$v['operator_time'])}}</td>
					<td>
						@if($v['isdel'] == 0)
							<button class="operator btn btn-mini btn-danger" type="button" data-isdel = {{$v['isdel']}} value="{{$v['id']}}" onclick="modify(this)">禁用</button>
						@else
							<button class="operator btn btn-mini btn-success" type="button" data-isdel = {{$v['isdel']}} value="{{$v['id']}}" onclick="modify(this)">启用</button>
						@endif
					</td>
				</tr>
				
			@endforeach
		@endif
	</table>
	{{$paginator->links()}}
<script type="text/javascript">
	function modify(obj){
		var isdel = $(obj).attr('data-isdel');
		var id = $(obj).val();
		if(confirm('确定执行该操作吗?')){
			$.post('/admin/modifyOperationUser',{id:id,isdel:isdel},function(data){
				if(data==-1){
					alert('修改失败');
					return;
				}
				alert('修改成功');
				window.location.reload();	
			});

		}
	}
</script>
@stop
