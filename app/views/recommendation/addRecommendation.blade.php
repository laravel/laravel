@extends('layouts.adIndex')
@section('title')
	精品推荐
@stop
@section('crumbs')
	精品推荐
@stop
@section('content')
	<form action="{{ url('/admin/doAddRecommendation') }}"
	        method="post"
	        enctype="multipart/form-data"
			>
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="150px">主标题</td>
				<td width="600px"><input class="form-control" id="title" name = "title" type="text" value=""></td>
			</tr>
			<tr>
				<td>副标题</td>
				<td>
					<textarea class="form-control" rows = '6' id = 'subhead' name = 'subhead'></textarea>
				</td>
			</tr>
			<tr>
				<td>图标</td>
				<td>
					<input class="file" id="formName" name="formName" type="file">
				</td>
			</tr>
			<tr>
				<td>下载地址</td>
				<td>
					<input class="form-control" id="url" name = "url" type="text" value="" />		  	
				</td>
			</tr>
			<tr>
				<td>排列顺序</td>
				<td><input class="form-control" id="sort" name="sort" type="input" value=""></td>
			</tr>
			<tr>
				<td>所属平台</td>
				<td>
				  	<input type="radio" name="platform"  value="0" checked>苹果
				  	<input type="radio" name="platform"  value="1">android
				</td>
			</tr>
			<tr text-align="center">
				<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="添加" /></td>
			</tr>
		</table>
	</form>
<script type="text/javascript">
	//分别检测主标题，副标题，下载地址，软件大小是否为空
	$('#title').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="titleerror" style="color:red;font-size=10px"> *主标题不能为空</span>';
			$('#titleerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#titleerror').remove();
		}
	});

	$('#subhead').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="subheaderror" style="color:red;font-size=10px"> *副标题不能为空</span>';
			$('#subheaderror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#subheaderror').remove();
		}
	});

	$('#url').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="urlerror" style="color:red;font-size=10px"> *下载地址不能为空</span>';
			$('#urlerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#urlerror').remove();
		}
	});

	// $('#size').focusout(function() {
	// 	var title = $(this).val();
	// 	if(title.length<=0) {
	// 		str = '<span id="sizeerror" style="color:red;font-size=10px"> *软件大小不能为空</span>';
	// 		$('#sizeerror').remove();
	// 		$(this).parent().prev().append(str);
	// 		return;
	// 	} else {
	// 		$('#sizeerror').remove();
	// 	}
	// });

	$('#sort').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="sorterror" style="color:red;font-size=10px"> *排列顺序不能为空</span>';
			$('#sorterror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#sorterror').remove();
		}
	});



</script>
@stop

