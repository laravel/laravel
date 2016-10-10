@extends('layouts.adIndex')
@section('title')
	添加伴奏
@stop
@section('crumbs')
	添加伴奏
@stop
@section('content')
	<form action="{{ url('/admin/doAdminAddPoem') }}"
	        method="post"
	        enctype="multipart/form-data"
			>
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="150px">伴奏名称</td>
				<td width="600px"><input type="text" class="form-control" id="name" name = "name" value=""></td>
			</tr>
			<tr>
				<td>所属分类</td>
				<td>
					@foreach($list as $key=>$item)
						@if($key%8 == 0 && $key != 0)
							<br/>
						@endif
						<input name="category[]" class="category" type="checkbox" value='{{$item['id']}}' />{{$item['category']}}
						&nbsp;&nbsp;&nbsp;&nbsp;
					@endforeach
				</td>
			</tr>
			<tr>
				<td>读者名字</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'readername' name = 'readername'></textarea>
				</td>
			</tr>
			<tr>
				<td>读者首字母(大写必填)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'readerfirstchar' name = 'readerfirstchar'></textarea>
				</td>
			</tr>
			<tr>
				<td>读者拼音(小写必填)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'readerpinyin' name = 'readerpinyin'></textarea>
				</td>
			</tr>
			<tr>
				<td>写者名字(必填)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'writername' name = 'writername'></textarea>
				</td>
			</tr>
			<tr>
				<td>写者首字母(大写必填)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'writerfirstchar' name = 'writerfirstchar'></textarea>
				</td>
			</tr>
			<tr>
				<td>写者拼音(小写必填)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'writerpinyin' name = 'writerpinyin'></textarea>
				</td>
			</tr>
			<tr>
				<td>伴奏写者分类</td>
				<td>
					<input type="radio" name="poemercatid" id = "poemercatid" value="1" />男
					<input type="radio" name="poemercatid" id = "poemercatid" value="2"  />女
				</td>
			</tr>
			<tr>
				<td>伴奏时长</td>
				<td>
					<input type = "text"  class="form-control" rows = '6' id = 'duration' name = 'duration'></textarea>
				</td>
			</tr>
			<tr>
				<td>诗拼音首字母(大写)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'spelling' name = 'spelling'></textarea>
				</td>
			</tr>
			<tr>
				<td>全拼首字母(小写)</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'allchar' name = 'allchar'></textarea>
				</td>
			</tr>
			<tr>
				<td>诗别名</td>
				<td>
					<input type = "text" class="form-control" rows = '6' id = 'aliasname' name = 'aliasname'></textarea>
				</td>
			</tr>
			<tr>
				<td>伴奏上传(.mp3)</td>
				<td><input type="file" name="formName1" /></td>
			</tr>
			<tr>
				<td>原唱上传(.mp3)</td>
				<td><input type="file" name="formName2" /></td>
			</tr>
			<tr>
				<td>歌词上传(.lrc)</td>
				<td><input type="file" name="formName3" /></td>
			</tr>
			<tr text-align="center">
				<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="添加" /></td>
			</tr>
		</table>
	</form>
<script type="text/javascript">
	//分别检测主标题，副标题，下载地址，软件大小是否为空
	$('#name').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="nameerror" style="color:red;font-size=10px"> *伴奏名称不能为空</span>';
			$('#nameerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#nameerror').remove();
		}
	});

	$('#readername').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="readernameerror" style="color:red;font-size=10px"> *读者名称不能为空</span>';
			$('#readernameerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#readernameerror').remove();
		}
	});

	$('#writername').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="writernameerror" style="color:red;font-size=10px"> *写者名称不能为空</span>';
			$('#writernameerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#writernameerror').remove();
		}
	});

	$('#duration').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="durationerror" style="color:red;font-size=10px"> *伴奏时长不能为空</span>';
			$('#durationerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#durationerror').remove();
		}
	});
	$('#spelling').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="spellingerror" style="color:red;font-size=10px"> *拼音首字母不能为空</span>';
			$('#spellingerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#spellingerror').remove();
		}
	});
	$('#allchar').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="allcharerror" style="color:red;font-size=10px"> *全拼首字母不能为空</span>';
			$('#allcharerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#allcharerror').remove();
		}
	});
	$('#aliasname').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="aliasnameerror" style="color:red;font-size=10px"> *诗别名不能为空</span>';
			$('#aliasnameerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#aliasnameerror').remove();
		}
	});



</script>
@stop

