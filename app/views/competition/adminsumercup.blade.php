@extends('layouts.adIndex')
@section('title')
	夏青杯审核列表
@stop
@section('crumbs')
	夏青杯审核列表
@stop
@section('search')
	<form action="/admin/addSumUser" method='post'>
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
				<td>添加夏青杯用户</td>
				<td><input type="submit" value="提交" /></td>
			</tr>
		</table>
	</form>
@stop
@section('content')
<script src="http://www.weinidushi.com.cn/js/My97DatePicker/WdatePicker.js"></script>	
<form action="/admin/getSumCupUserList" method='get'>
    <table  style="width:90%; margin-bottom:20px;">
        <tr>
            <td>UID：</td>
            <td><input name="uid" type='text' id="uid" value="{{$data['uid']}}" class="form-control" style="width:120px;" /></td>
            <td>真实姓名：</td>
            <td><input name="name" type='text' id="name" value="{{$data['name']}}" class="form-control" style="width:120px;" /></td>
            <td>手机号码：</td>
            <td><input name="mobile" type='text' id="mobile" value="{{$data['mobile']}}" class="form-control" style="width:120px;" /></td>
            <td>电子邮箱：</td>
            <td><input name="email" type='text' id="email" value="{{$data['email']}}" class="form-control" style="width:200px;" /></td>
        </tr>
        <tr>
            <td>开始时间：</td>
            <td><input name="sdate" type='text' id="sdate" value="{{$data['sdate']}}" class="form-control" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" style="width:120px;" /></td>
            <td>结束时间：</td>
            <td><input name="edate" type='text' id="edate" value="{{$data['edate']}}" class="form-control" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" style="width:120px;" /></td>
            <td>用户昵称：</td>
            <td><input name="nick_name" type='text' id="nick_name" value="{{$data['nick_name']}}" class="form-control" style="width:120px;" /></td>
            <td colspan="2">
            <input type="submit" value="搜索" class="btn btn-mini btn-success" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="http://www.weinidushi.com.cn/admin/summCupXls" id="btn-dao" target="_blank">导出xls</a>
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
	
	var url="http://www.weinidushi.com.cn/admin/summCupXls";
	var url_str="?uid="+uid;
	url_str+="&name="+name;
	url_str+="&mobile="+mobile;
	url_str+="&email="+email;
	url_str+="&sdate="+sdate;
	url_str+="&edate="+edate;
	url_str+="&nick_name="+nick_name;
	
	$(this).attr("href",url+url_str);
	return true;
	
});
</script>   
<div class="table-responsive">
	<table class="table table-hover table-bordered">
		<tr>
			<th>id</th>
			<th>uid</th>
			<th style="width:20px">身份证</th>
			<th>姓名</th>
			<th>昵称</th>
			<th>电话</th>
			<th>邮箱</th>
			<th>申请日期</th>
			<th>支付时间</th>
			<th>支付状态</th>
		</tr>
        @if($data['total']>0)
		@foreach ($cupList as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>{{$item['card']}}</td>
			<td>{{$item['name']}}</td>
			<td>{{$item['nick_name']}}</td>
			<td>{{$item['mobile']}}</td>
			<td>{{$item['email']}}</td>
			<td>{{date('Y/m/d H:i',$item['addtime'])}}</td>
			<td>{{date('Y/m/d H:i',$item['updatetime'])}}</td>
			
			<td>支付成功</td>
		</tr>
        <tr>
        	<td colspan="10">
            姓名：{{$item['name']}}<br>
            单位名称：{{$item['company']}}<br>
            地址：{{$item['address']}}&nbsp;&nbsp;&nbsp;邮编：{{$item['zip']}}<br>
            </td>
        </tr>
		@endforeach
        @endif
	</table>
</div>
    {{ $cupList->appends(array('name'=>$data['name'],'uid'=>$data['uid'],'email'=>$data['email'],'mobile'=>$data['mobile'],'sdate'=>$data['sdate'],'edate'=>$data['edate'],'nick_name'=>$data['nick_name']))->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$data['total']}}</em> 条记录</a></li></ul>
<script type="text/javascript">
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



