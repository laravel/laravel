@extends('layouts.adIndex')
@section('title')
	诵读联合会用户列表
@stop
@section('crumbs')
	诵读联合会用户列表
@stop
@section('search')
	<form action="/admin/addLeagueUser" method='post'>
		<table>
			<tr>
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
				<td>添加诵读联合会用户</td>
				<td><input type="submit" value="提交" /></td>
			</tr>
		</table>
	</form>
@stop


@section('content')
<script src="http://poem.weinidushi.com.cn/upload/js/My97DatePicker/WdatePicker.js"></script>	
    <form action="/admin/admin_league_list" method='get'>
		<table  style="width:90%; margin-bottom:20px;">
			<tr>
				<td>UID：</td>
				<td><input name="uid" type='text' id="uid" value="{{$data['uid']}}" class="form-control" style="width:100px;" /></td>
				<td>真实姓名：</td>
                <td><input name="name" type='text' id="name" value="{{$data['name']}}" class="form-control" style="width:100px;" /></td>
				<td>手机号码：</td>
                <td><input name="mobile" type='text' id="mobile" value="{{$data['mobile']}}" class="form-control" style="width:100px;" /></td>
                <td>电子邮箱：</td>
                <td><input name="email" type='text' id="email" value="{{$data['email']}}" class="form-control" style="width:140px;" /></td>
                <td>次数：</td>
                <td>
                <select id="grouby" name="grouby" class="form-control">
                	<option value="0" <?php echo $data['grouby']==0?'selected':'';?>>不限</option>
                    <option value="1" <?php echo $data['grouby']==1?'selected':'';?>>支付一次以上</option>
                </select>
                </td>
			</tr>
            <tr>
				<td>开始时间：</td>
				<td><input name="sdate" type='text' id="sdate" value="{{$data['sdate']}}" class="form-control" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" style="width:120px;" /></td>
				<td>结束时间：</td>
                <td><input name="edate" type='text' id="edate" value="{{$data['edate']}}" class="form-control" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" style="width:120px;" /></td>
				<td>用户昵称：</td>
                <td><input name="nick_name" type='text' id="nick_name" value="{{$data['nick_name']}}" class="form-control" style="width:120px;" /></td>
                <td>状态：</td>
                <td>
                <select id="status" name="status" class="form-control">
                	<option value="-1" <?php echo $data['status']==-1?'selected':'';?>>不限</option>
                    <option value="0" <?php echo $data['status']==0?'selected':'';?>>未审核</option>
                    <option value="1" <?php echo $data['status']==1?'selected':'';?>>审核失败</option>
                    <option value="2" <?php echo $data['status']==2?'selected':'';?>>已通过审核</option>
                </select>
                </td>
                <td colspan="2">
                <input type="submit" value="搜索" class="btn btn-mini btn-success" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="http://www.weinidushi.com.cn/admin/leagueXls" id="btn-dao" target="_blank">导出xls</a>
                </td>
			</tr>
		</table>
	</form>
<script language="javascript">
$("#btn-dao").click(function(){
	var uid=$("#uid").val();
	var name=$("#name").val();
	var mobile=$("#mobile").val();
	var email=$("#email").val();
	var sdate=$("#sdate").val();
	var edate=$("#edate").val();
	var nick_name=$("#nick_name").val();
	var status=$("#status").val();
	var grouby=$("#grouby").val();
	
	var url="http://www.weinidushi.com.cn/admin/leagueXls";
	var url_str="?uid="+uid;
	url_str+="&name="+name;
	url_str+="&mobile="+mobile;
	url_str+="&email="+email;
	url_str+="&sdate="+sdate;
	url_str+="&edate="+edate;
	url_str+="&nick_name="+nick_name;
	url_str+="&status="+status;
	url_str+="&grouby="+grouby;
	
	$(this).attr("href",url+url_str);
	return true;
	
});
</script>   
	<table class="table table-hover table-bordered ">
		<tr>
			<th>申请ID</th>
			<th>用户ID</th>
			<th>真名<i class="icon-search"></i></th>
			<th width="100px">用户昵称</th>
			<th>性别</th>
            <th>身份证号</th>
			<th>移动电话</th>
			<th>电子邮箱</th>
            <th>作品数量</th>
			<th>申请时间</th>
			<th>通过时间</th>
            <!--th>查看订单</th-->
			<th>审核状态</th>

		</tr>
		@foreach ($leagueList as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>{{$item['name']}}</td>
			<td style="width:100px">{{$item['nick']}}</td>
            <td>
            @if($item['gender']==1)
                男
            @else
                女
            @endif
            </td>
			<td>{{$item['card']}}</td>
			<td>{{$item['mobile']}}</td>
			<td>{{$item['email']}}</td>
            <td>
            <a href="/admin/opusList?uid={{$item['uid']}}" target="_blank">
            <?php
            if(isset($data['user_num'][$item['uid']])){
				echo $data['user_num'][$item['uid']];
			}else{
				echo 0;
			}
			?>
            </a>
            </td>
			<td>{{date('Y/m/d H:i',$item['addtime'])}}</td>
            @if($item['audit_time'] == 0)
            <td>-</td>
            @else
            <td>{{date('Y/m/d H:i',$item['audit_time'])}}</td>
            @endif
			<!--td><a href="">看订单</a></td-->
			@if(!empty($order_ids) && in_array($item['orderid'],$order_ids))
				<td><button class="operator btn btn-mini btn-success" type="button"  data-id="{{$item['orderid']}}" value=''> 自动通过</button></td>
			@elseif(empty($item['audit_time']))
				<td><button class="operator btn btn-mini btn-danger" type="button"  data-id="{{$item['orderid']}}" value=''>审核中</button></td>
			@elseif($item['status'] == 1)
				<td><button class="operator btn btn-mini btn-warning" type="button"  data-id="{{$item['orderid']}}" value=''>审核失败</button></td>
			@else
				<td><button class="operator btn btn-mini btn-success" type="button"  data-id="{{$item['orderid']}}" value=''>已通过审核</button></td>
			@endif
			<!-- <td><button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|{{$item['uid']}}'>删除作品</button></td> -->
		</tr>
        <tr>
        	<td colspan="11">
            单位名称：{{$item['company']}}<br>
            地址：{{$item['address']}}&nbsp;&nbsp;&nbsp;邮编：{{$item['zip']}}<br>
            申请入会理由：{{$item['cause']}}
            </td>
        </tr>
		@endforeach
	</table>
	{{ $leagueList->appends(array('status'=>$data['status'],'name'=>$data['name'],'uid'=>$data['uid'],'email'=>$data['email'],'mobile'=>$data['mobile'],'sdate'=>$data['sdate'],'edate'=>$data['edate'],'nick_name'=>$data['nick_name']))->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$data['total']}}</em> 条记录</a></li></ul>
<script type="text/javascript">
	$(function(){
		$('.operator').each(function(){
			$(this).click(function(){
				var orderid = $(this).attr('data-id');
				$.post('/admin/pass_league',{orderid:orderid},function(data)
				{
					if(data == -1)
					{
						alert('用户id错误，请重试');
						return;
					}
					else if (data == -2)
					{
						alert('该用户没有未审核的订单');
						return;
					}
					else
					{
						alert('通过审核');
						window.location.reload();
					}
				});
				
			});
		});
	});

	//根据用户昵称，手机，邮件查找用户
	$('.search').click(function() {
		var nick = $('#nick').val();
		if(nick.length<=0) {
			alert('搜索条件不能全部为空');
		}
		$.post('/admin/searchUser',{nick:nick},function(data) {
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


