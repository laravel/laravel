@extends('layouts.adIndex')
@section('title')
	添加作品
@stop
@section('search')
	<table>
		<tr>
			<td>用户昵称</td>
			<td style="width:300px"><input id = 'nick' name="nick" type='text' class="form-control" /></td>
			<td>选择具体用户</td>
			<td>
				<select name="username" class="username form-control">
				</select>
			</td>
		</tr>
		<tr>
			<td>伴奏名称</td>
			<td>
				<input type="text" class="form-control" name="poemname" id="poemname" />
			</td>
			<td>选择具体伴奏</td>
			<td>
				<select name="detailpoem" class="detailpoem form-control">
				</select>
			</td>
		</tr>
	</table>

@stop
@section('crumbs')
	添加作品
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<form action="{{ url('/admin/doAddOpus') }}"
	        method="post"
	        enctype="multipart/form-data"
			>
			<table class="table table-hover table-bordered ">
				<tr>
					<td width="200px">用户id</td>
					<td><input class="form-control input-xxlarge" id="userid" name = "userid" type="text" value=""></td>
				</tr>
				<tr>
					<td>伴奏id</td>
					<td>
						<input class="form-control input-xxlarge" id="poemid" name = "poemid" type="text" value="">		  	
					</td>
				</tr>
				<tr>
					<td>作品名称</td>
					<td><input class="form-control input-xxlarge" id="opusname" name="opusname" type="text" value=""></td>
				</tr>
				<tr>
					<td>作品首字母(大写)</td>
					<td>
					  	<input class="form-control input-xxlarge" id="firstchar" name="firstchar" type="text" value="" />
					</td>
				</tr>
				<tr>
					<td>作品拼音首字母(小写)</td>
					<td>
						<input class="form-control input-xxlarge" id="pinyin" name="pinyin" teyp="text" value="" />
					</td>
				</tr>
				<tr>
					<td>作品时长(秒整数)</td>
					<td>
						<input class="form-control input-xxlarge" id="opustime" name="opustime" teyp="text" value="" />
					</td>
				</tr>
				<tr>
					<td>作品歌词</td>
					<td>
						<input type="file" name="lyricName" />	  	
					</td>
				</tr>
				<tr>
					<td>上传作品</td>
					<td>
						<input type="file" name="opus" />
					</td>
				</tr>
				<tr text-align="center">
					<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
				</tr>
			</table>
		</form>
	</table>
	<script type="text/javascript">
		$('.username').on('click',function() {
			var getuserid = $(this).val();
			// $('#userid').attr("value",getuserid);
			$('#userid').val(getuserid);
		});

		$('.detailpoem').on('click',function() {
			var getpoemid = $(this).val();
			// $('#poemid').attr('value',getpoemid);
			$('#poemid').val(getpoemid);
		});

		//根据用户昵称获取用户名，用户id
		$(function() {
			$('#nick').focusout(function() {
				var nick = $(this).val();
				if(nick.length<=0) {
					alert('请输入用户昵称');
					return;
				}
				$.post('/admin/accordNickFind',{nick:nick,flag:1},function(data) {
					if('error' == data) {
						alert('没有查到相关数据，请重试');
						return;
					} else {
						$('.username').empty();
						$('.username').append(data);
					}
				});
			});
		});

		//根据伴奏名称获取伴奏名称，伴奏id
		$(function() {
			$('#poemname').focusout(function() {
				var detailpoem = $(this).val();
				if(detailpoem.length<=0) {
					alert('请输入作品名称');
					return;
				}
				$.post('/admin/accordNickFind',{detailpoem:detailpoem,flag:2},function(data) {
					if('error' == data) {
						alert('没有查到相关数据，请重试');
						return;
					} else {
						$('.detailpoem').empty();
						$('.detailpoem').append(data);
					}
				});
			});
		});

	</script>
@stop

