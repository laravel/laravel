@extends('layouts.adIndex')
@section('title')
	用户列表
@stop
@section('search')
	<form action="/admin/GetUserList" method='get'>
		<table>
			<tr>
				<td>UID</td>
				<td><input id = 'uid' name="uid" type='text' class="form-control" value="<?php echo !empty($uid) ? $uid : ''?>" style="width:100px;" /></td>
                <td>用户昵称</td>
				<td><input id = 'nick' name="nick" type='text' class="form-control" value="{{$nick}}" style="width:100px;" /></td>
                <td>手机号</td>
				<td><input id = 'phone' name="phone" type='text' class="form-control" value="{{$phone}}" /></td>
				<td>
					<input type="text" id="starttime" name="starttime"  class="form-control"  value="<?php if(!empty($starttime)) {echo $starttime;} else {echo "开始时间";}?>"/>
				</td>
				<td>
					<input type="text" id="endtime" class="form-control"  name="endtime" value="<?php if(!empty($endtime)){ echo $endtime;}else{echo "结束时间";}?>"/>
				</td>
			</tr>
			<tr>
                <td>是否认证</td>
				<td>
                <select id="authtype" name="authtype" class="form-control" style="width:100px;">
                	<option value="-1">全部</option>
                    <option <?php if($authtype == 0) echo 'selected'?> value="0">未认证</option>
                    <option <?php if($authtype == 1) echo 'selected'?> value="1">已认证</option>
                </select>
                </td>
                <td>是否删除</td>
				<td>
                <select id="isdel" name="isdel" class="form-control" style="width:100px;">
                	<option value="-1">全部</option>
                    <option <?php if($isdel == 0) echo 'selected'?> value="0">正常</option>
                    <option <?php if($isdel == 1) echo 'selected'?> value="1">已删除</option>
                </select>
                </td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />
				</td>
			<tr>
			<!--
			<tr>
				<td><a id="btn-dao" onclick="execl()" href="/admin/GetUserList?flag" target="_blank">导出xls</a></td>
			</tr>
			-->
		</table>
	</form>
@stop
@section('crumbs')
	<?php
    $arr=array('-1'=>'','0'=>'未认证','1'=>'已认证');
	$all_del=array('-1'=>'',0=>'正常',1=>'已删除');
	?>
	用户列表:<?php echo $arr[$authtype].$all_del[$isdel];?>用户总数{{$count}}
	<?php echo "用户注册数目".":".$addtimecount;?>
@stop
@section('content')
<style type="text/css">
.sex{ background-color:transparent; border:0px; width:50px; }
.sex2{ background-color:#FFF; border:1px solid #CCC; width:50px; }
</style>
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id<i class="icon-search"></i></th>
			<th>昵称</th>
            <th>姓名</th>
			<th>邮箱</th>
			<th>手机号</th>
			<th>性别</th>
			<th>等级</th>
			<th>注册时间</th>
			<th>来源</th>
			<th>认证类型</th>
			<th>认证信息</th>
			<th>是否童星</th>
			<th>禁用/启用</th>
		</tr>
		@foreach ($userlist as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td><input class="nick_inp" data-id="{{$item['id']}}" value="{{$item['nick']}}" style="width:100px;" /> </td>
            <td><input class="real_inp" data-id="{{$item['id']}}" value="{{$item['real_name']}}" style="width:100px;" /></td>
			<td>{{$item['email']}}</td>
			<td>{{$item['phone']}}</td>
			<td>
            	<input type="text" name="sex" data-id="<?php echo $item['id'];?>" class="sex" value="<?php echo $item['gender']==1?'男':'女';?>" />
			</td>
			<td>{{$item['grade']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				@if($item['thpartType'] == 0) 
					<button class="btn btn-mini btn-info" type="button" >本系统</button>
				@elseif($item['thpartType'] == 1) 
					<button class="btn btn-mini btn-info" type="button" >新浪</button>
                @elseif($item['thpartType'] == 3) 
					<button class="btn btn-mini btn-info" type="button" >微信</button>    
				@else
					<button class="btn btn-mini btn-info" type="button" >QQ</button>
				@endif
			</td>
			<td>
				@if($item['authtype'] == 0)
					<button class="authStatus btn btn-mini btn-success" type="button" value='{{$item['id']}}|0'>添加认证</button>
				@elseif($item['authtype'] == 1)
					<button class="authStatus btn btn-mini btn-danger" type="button" value='{{$item['id']}}|1'>取消认证</button>
				@endif
			</td>
			<td>
				<!-- <input style="width:200px;height:100px" type="text" class="authContent input-xxlarge" value='{{$item['authconent']}}' /> -->
				<textarea type="text" rows="6" class='authContent'>{{$item['authconent']}}</textarea>
				<input type="hidden" value='{{$item['id']}}' />
			</td>
			<td>
				@if($item['teenager'] == 0)
					<button class="teenager btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|0'>非童星</button>
				@elseif($item['teenager'] == 1) 
					<button class="teenager btn btn-mini btn-success" type="button" value='{{$item['id']}}|1'>童星</button>
				@endif
			</td>
			<td>
				@if($item['isdel'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|0'>禁用</button>
				@elseif($item['isdel'] == 1) 
					<button class="operator btn btn-mini btn-success" type="button" value='{{$item['id']}}|1'>启用</button>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $userlist->appends(array('nick'=>$nick,'isdel'=>$isdel,'authtype'=>$authtype,'uid'=>$uid,'phone'=>$phone,'starttime'=>$starttime,'endtime'=>$endtime))->links()  }}
<script type="text/javascript">
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var uidSign = $(this).val();
				var arr = uidSign.split('|');
				var uid = arr[0];
				var sign = arr[1];
				$.post('/admin/delOrDelUser',{uid:uid,sign:sign},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
		//修改昵称
		$(".nick_inp").blur(function(){
			var id=$(this).attr("data-id");
			var nick=$(this).val();
			$.get('/admin/upUserName',{id:id,nick:nick},function(data){
				//修改成功
			});
		});
		//修改真实姓名
		$(".real_inp").blur(function(){
			var id=$(this).attr("data-id");
			var real_name=$(this).val();
			$.get('/admin/upUserName',{id:id,real_name:real_name},function(data){
				//修改成功
			});
		});
		//修改性别
		$(".sex").focus(function(){
			$(this).addClass("sex2");
		});
		$(".sex").blur(function(){
			var id=$(this).attr("data-id");
			var val=$(this).val();
			var sex= val=='男' ? 1 : 2;
			var _t=$(this);
			$.get('/admin/upUserName',{id:id,sex:sex},function(data){
				//修改成功
				_t.removeClass("sex2");
			});
		});
	});

	//添加/取消认证
	$(function() {
		$('.authStatus').each(function() {
			$(this).click(function() {
				var authStr = $(this).val();
				var authArr = authStr.split('|');
				var uid = authArr[0];
				var sign = authArr[1];
				$.post('/admin/userAuthStatus',{uid:uid,sign:sign},function(data) {
					if('error'==data) {
						alert('添加认证失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	//修改认证信息
	$(function() {
		$('.authContent').each(function() {
			$(this).focusout(function() {
				var authconent = $(this).val();
				var uid = $(this).next().val();
				$.post('/admin/modifyAuthContent',{uid:uid,authconent:authconent},function(data) {
					if('error' == data) {
						alert('修改认证信息失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
	//修改或者添加记录日志
	$(function() {
		$('.authContent').each(function() {
			$(this).focusout(function() {
				var authconent = $(this).val();
				var uid = $(this).next().val();
				$.post('/admin/userAuthContent',{uid:uid,authconent:authconent},function(data
					) {
					if('error' == data) {
						alert('修改认证信息失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
	//修改是否为童星
	$(function() {
		$('.teenager').each(function() {
			$(this).click(function() {
				var teenagerStr = $(this).val();
				var teenagerArr = teenagerStr.split('|');
				var uid = teenagerArr[0];
				var sign = teenagerArr[1];
				$.post('/admin/userTeenager',{uid:uid,sign:sign},function(data) {
					if('error'==data) {
						alert('修改失败,请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
	$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$("#province_id").change(function(){
			//重置
			$("#city_id").empty();
			$("#area_id").empty();
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getCity",{province_id:_val},function(data){
					var html="<option>全部</option>";
					$.each(data, function(i, field){
						html+='<option value="'+field.id+'">'+field.name+'</option>';
					});
					$("#city_id").append(html);
				});
			}
		});
		function execl(){
		
		var uid=$("input[name='uid']").val();
		var nick=$("input[name='nick']").val();
		var phone=$("input[name='phone']").val();
		var starttime=$("input[name='starttime']").val();
		var endtime=$("input[name='endtime']").val();
		var authtype=$("#authtype").val();
		var isdel=$("#isdel").val();

		var url="/admin/getUserListXls";
		var url_str="?uid="+uid;
		url_str+="&nick="+nick;
		url_str+="&phone="+phone;
		url_str+="&starttime="+starttime;
		url_str+="&endtime="+endtime;
		url_str+="&authtype="+authtype;
		url_str+="&isdel="+isdel;

		var tourl=url+url_str;
		 
	$("#btn-dao").attr("href",tourl);
	return true;
	}
</script>
@stop

