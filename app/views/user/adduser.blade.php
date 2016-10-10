@extends('layouts.adIndex')
@section('title')
	添加用户
@stop
@section('crumbs')
	添加用户
@stop
@section('content')
	<form action ='/admin/addUserPost' method = 'post' enctype="multipart/form-data" >
	<table class="table table-hover table-bordered ">
		<tr>
			<td width="200px">昵称</td>
			<td><input class="form-control input-xxlarge" id="nick" name = "nick" type="text" value=""></td>
		</tr>
		<tr>
			<td width="200px">用户头像</td>
			<td><input class="form-control input-xxlarge" id="nick" name = "portrait" type="file" value=""></td>
		</tr>
		<tr>
			<td>手机号码</td>
			<td>
				<input class="form-control input-xxlarge" id="email" name = "email" type="text" value="">		  	
			</td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input class="form-control input-xxlarge" id="pwd" name="password" type="password" value=""></td>
		</tr>
		<tr>
			<td>性别</td>
			<td>
			  	<input type="radio" name="gender"  value="1" checked>男
			  	<input type="radio" name="gender"  value="0">女
			</td>
		</tr>
		<tr>
			<td>是否为童星</td>
			<td>
				<input type="radio" name="teenager"  value="1" >是
				<input type="radio" name="teenager"  value="0" checked>否
			</td>
		</tr>
		<tr>
			<td>VIP认证</td>
			<td>
				<input type="radio" name="authtype"  value="1" >是
				<input type="radio" name="authtype"  value="0" checked>否
			</td>
		</tr>
		<tr text-align="center">
			<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
		</tr>
	</table>
	</form>
<script type="text/javascript">
	$('#nick').focus();
	$('#nick').focusout(function() {
		var nick = $('#nick').val();
		str = '<span id="nickerror" style="color:red;font-size=5px"> *用户昵称不能超过5个字符或者为空</span>';
		if(nick.length>5 || nick.length<1) {
			$('#nickerror').remove();
			$(this).after(str);
			return;
		} else {
			$('#nickerror').remove();
		}
		$.post('/admin/addUserPost',{nick:nick},function(data) {
			if('exists' == data) {
				str = '<span id="nickerror" style="color:red;font-size=10px"> *昵称已存在</span>';
				$('#nickerror').remove();
				$('#nick').after(str);
				return;
			} else {
				$('#nickerror').remove();
			}
		});
	});

	//邮件或手机号码验证
	$('#email').focusout(function() {
		var email = $('#email').val();
		//验证是手机号还是email
		str = '<span id="emailerror" style="color:red;font-size=5px"> *电子邮件/手机号不能为空</span>';
		if(email.length<1) {
			$('#emailerror').remove();
			$(this).after(str);
			return;
		} else {
			$('#emailerror').remove();
		}
		$.post('/admin/addUserPost',{email:email},function(data) {
			if('eperror' == data) {
				str = '<span id="emailerror" style="color:red;font-size=10px"> *电子邮件/手机号已存在</span>';
				$('#emailerror').remove();
				$('#email').after(str);
				return;
			} else if('formaterror' == data) {
				$('#emailerror').remove();
				str = '<span id="emailerror" style="color:red;font-size=10px"> *电子邮件/手机号格式错误</span>';
				$('#email').after(str);	
				return;
			} else {
				$('#emailerror').remove();
			}
		});
	});
	//密码
	$('#pwd').focusout(function() {
		str = '<span id="pwderror" style="color:red;font-size=5px"> *密码不能为空</span>';
		var password = $('#pwd').val();
		if(password.length < 1) {
			$('#pwderror').remove();
			$(this).after(str);
			return;
		} else {
			$('#pwderror').remove();
		}
	});

</script>
@stop

