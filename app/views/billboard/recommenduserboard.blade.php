@extends('layouts.adIndex')
@section('title')
	推荐博主
@stop
@section('search')
	<form action="/admin/addRecommendUser" method='post'>
		<table>
			<tr>
				<td>UID</td>
                <td><input id = 'uid' name="uid" type='text' class="form-control" /></td>
                <td>用户昵称</td>
				<td style="width:300px"><input id = 'nick' name="nick" type='text' class="form-control" /></td>
				<td colspan=2>
					<button class="search btn btn-mini btn-success" type="button"  value=''>查询</button>
				</td>
			</tr>
			<tr>
				<td>查询结果</td>
				<td style="width:300px" colspan=4>
					<select id = 'lastdata' name='lastdata' class="form-control">
					</select>
				</td>
			<tr>
			<tr>
				<td>博主排序</td>
				<td><input type="text" id = 'usersort' name='usersort' class='form-control' value='' /></td>
				<td colspan=4>
					<input type='submit' value="提交"/>
				</td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	推荐博主
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>用户排行<i class="icon-search"></i></th>
			<th>用户id</th>
			<th>昵称</th>
            <th>性别</th>
			<th>邮箱</th>
			<th>手机号</th>
			<th>来源</th>
			<th>删除排序</th>
		</tr>
		@foreach ($recommenduserboard as $item)
		<tr>
			<td><input type = 'text' name='recommendopus' class='recommendopus' value='{{$item['recommenduser']}}'/></td>
			<td>{{$item['id']}}</td>
			<td>{{$item['nick']}}</td>
            <td>
            @if($item['gender']==1)
                男
            @else
                女
            @endif
            </td>
			<td>{{$item['email']}}</td>
			<td>{{$item['phone']}}</td>
			<td>
				@if($item['thpartType'] == 0) 
					<button class="operator btn btn-mini btn-danger" type="button" >本系统</button>
				@elseif($item['thpartType'] == 1) 
					<button class="operator btn btn-mini btn-danger" type="button" >新浪</button>
				@else
					<button class="operator btn btn-mini btn-danger" type="button" >QQ</button>
				@endif
			</td>
			<td>
				<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|{{$item['recommenduser']}}'>删除排序</button>
			</td>
		</tr>
		@endforeach
	</table>
	{{ $recommenduserboard->links()  }}
<script type="text/javascript">
	//删除推荐博主
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var uidSign = $(this).val();
				var arr = uidSign.split('|');
				var uid = arr[0];
				var  recommenduser = arr[1];
				$.post('/admin/delRecommendUser',{uid:uid,recommenduser:recommenduser},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	//修改推荐博主顺序
	$(function() {
		$('.recommendopus').each(function() {
			$(this).focusout(function() {
				var recommenduser = $(this).val(); //新的排列顺序
				var uid = $(this).parent().next().html();//用户id
				$.post('/admin/modifyRecommendUserSort',{uid:uid,recommenduser:recommenduser},function(data) {
					if('error' == data) {
						alert('操作失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	//根据用户昵称，手机，邮件查找用户
	$('.search').click(function() {
		var nick = $('#nick').val();
		var uid = $('#uid').val();
		if(nick=='' && uid=='') {
			alert('搜索条件不能全部为空');
			return ;
		}
		$.post('/admin/searchUser',{nick:nick,uid:uid},function(data) {
			if('error'==data) {
				alert('查询失败,请查看搜索条件');
				return;
			} else {
				$('#lastdata').empty();
				$('#lastdata').append(data);
			}
		});
	});
</script>
@stop

