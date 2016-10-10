@extends('layouts.adIndex')
@section('title')
	认证用户
@stop
@section('search')
	<form action="/admin/authorUserList" method='get'>
		<table>
			<tr>
				<td>用户id</td>
				<td>
					<input type="text" name="id" type="text" class="form-control" >
				</td>
				<td>用户昵称</td>
				<td style="width:200px">
					<input id = 'nick' name="nick" type='text' class="form-control" value={{$nick}} >
				</td>
				<td>开始时间</td>
                <td width="160"><input type="text" name="startTime" id="startTime"  class="form-control" value={{$startTime}}></td>
				<td>结束时间</td>
                <td width="160"><input type="text" name="endTime" id="endTime" class="form-control" value={{$endTime}}></td>
                <td>
					<select name = "authtype" class="form-control" style="width:100px;">
						<option value=0 @if($authtype==0) selected="selected" @endif>未认证</option>
						<option value=1 @if($authtype==1) selected="selected" @endif>已认证</option>
					</select>                	
                </td>
                <input type="hidden" id = 'authtype2' name="authtype2" value="{{$authtype}}" />
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />
				</td>
				<td colspan=2>
					<input class="exportexcel btn btn-mini btn-success" type="button"  value='导出excel' />
				</td>
				<td>
					<div id="downexcel"></div>
				</td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	认证申请
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id<i class="icon-search"></i></th>
			<th>用户id</th>
			<th>昵称</th>
            <th>性别</th>
			<th width="80">真实姓名</th>
			<th>手机号码</th>
			<th>认证内容</th>
            <th>作品数量</th>
			<th width="100">申请时间</th>
			<th>通过/取消</th>
		</tr>
		@foreach ($userlist as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>
            <?php
            if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['nick'];
			}else{
				echo $item['nick'];
			}
			?>
            </td>
            <td>
            <?php
            if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['gender']==1?'男':'女';
			}
			?>
            </td>
			<td>{{$item['realname']}}</td>
			<td>{{$item['telphone']}}</td>
			<td>{{$item['content']}}</td>
            <td>
            <a href="/admin/opusList?uid={{$item['uid']}}&type=-1&isread=-1&isdel=0" target="_blank">
            <?php
            if(isset($user_num[$item['uid']])){
				echo $user_num[$item['uid']];
			}else{
				echo 0;
			}
			?>
            </a>
            </td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>	
			<td>
				@if($item['status'] == 0)
					<button class="authStatus btn btn-mini btn-success" type="button" value='{{$item['id']}}' data-status='0'>通过</button>
				@elseif($item['status'] == 1)
					<button class="authStatus btn btn-mini btn-danger" type="button" value='{{$item['id']}}' data-status='1'>取消</button>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $userlist->appends(array('nick'=>$nick,'startTime'=>$startTime,'endTime'=>$endTime,'authtype'=>$authtype,'id'=>$id))->links() }}
	<ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$total}}</em> 条记录</a></li></ul>
<script type="text/javascript">
	$(function() {
		$('.authStatus').each(function(){
			$(this).click(function() {
				var id = $(this).val();
				var status = $(this).attr('data-status');
				alert(status);return;
				$.post('/admin/checkAuthorUser',{id:id,status:status},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
	$(function(){
		$('.exportexcel').click(function(){
			var startTime = $('#startTime').val();
			var endTime = $('#endTime').val();
			var nick = $('#nick').val();
			var authtype =  $('#authtype2').val();
			$.get('/admin/authorUserList',{nick:nick,startTime:startTime,endTime:endTime,flag:1,authtype:authtype},function(data){
				if(data==-1)
				{
					alert('导出失败');
				}
				else
				{
					$('#downexcel').append(data);
					alert('导出成功');
				}
			});
		});
	});
	$(function() {
    	$( "#startTime" ).datepicker();
    	$( "#endTime" ).datepicker();
  	});
</script>
@stop

